<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index()
    {
        $employees = User::whereIn('role', ['admin', 'waiter', 'kitchen', 'cashier'])->paginate(5, ['*'], 'employees');
        $customers = User::where('role', 'customer')->paginate(5, ['*'], 'customers');
        return view('Dashboard.users', compact('employees', 'customers'));
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'role' => ['required', Rule::in(['admin', 'waiter', 'kitchen', 'cashier', 'customer'])],
        ]);

        try {
            // Transaction başlat
            DB::beginTransaction();
            
            // Kullanıcıyı oluştur
            $user = User::create([
                'name' => $request->name,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role,
            ]);

            // Eğer kullanıcı müşteri ise, Customer tablosuna da kayıt ekle
            if ($request->role === 'customer') {
                Customer::create([
                    'user_id' => $user->id,
                ]);
                \Log::info('Yeni müşteri kaydı oluşturuldu (kullanıcı oluşturma sırasında): User ID: ' . $user->id);
            }
            
            // İşlemleri onayla
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla eklendi',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Hata durumunda geri al
            DB::rollBack();
            \Log::error('Kullanıcı oluşturulurken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı oluşturulurken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'role' => ['required', Rule::in(['admin', 'waiter', 'kitchen', 'cashier', 'customer'])],
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;

        try {
            // Transaction başlat
            DB::beginTransaction();
            
            // Rol değişikliği varsa
            if ($oldRole !== $newRole) {
                // Kullanıcının rolü customer iken başka bir role değiştiriliyorsa
                if ($oldRole === 'customer' && $newRole !== 'customer') {
                    // Customer tablosundaki kaydını bul ve sil
                    $customer = Customer::where('user_id', $user->id)->first();
                    if ($customer) {
                        // Eğer bu müşterinin siparişleri varsa, siparişleri misafir olarak işaretle
                        $customer->orders()->update(['customer_id' => null]);
                        
                        // Müşteri kaydını sil
                        $customer->delete();
                        
                        \Log::info('Müşteri kaydı silindi: User ID: ' . $user->id);
                    }
                }
                // Eğer kullanıcı başka bir rolden customer rolüne değiştiriliyorsa yeni müşteri kaydı oluştur
                elseif ($oldRole !== 'customer' && $newRole === 'customer') {
                    // Customer tablosunda kayıt yoksa oluştur
                    if (!Customer::where('user_id', $user->id)->exists()) {
                        Customer::create([
                            'user_id' => $user->id
                        ]);
                        
                        \Log::info('Yeni müşteri kaydı oluşturuldu: User ID: ' . $user->id);
                    }
                }
            }

            $userData = [
                'name' => $request->name,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $newRole,
            ];

            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'string|min:6',
                ]);
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);
            
            // İşlemleri onayla
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla güncellendi',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Hata durumunda geri al
            DB::rollBack();
            \Log::error('Kullanıcı güncellenirken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        try {
            // Transaction başlat
            DB::beginTransaction();
            
            // Eğer kullanıcı müşteri ise, müşteri kaydını da sil
            if ($user->role === 'customer') {
                $customer = Customer::where('user_id', $user->id)->first();
                if ($customer) {
                    // Müşterinin siparişlerini misafir olarak işaretle
                    $customer->orders()->update(['customer_id' => null]);
                    
                    // Müşteri kaydını sil
                    $customer->delete();
                    
                    \Log::info('Müşteri kaydı silindi (kullanıcı silinirken): User ID: ' . $user->id);
                }
            }
            
            $user->delete();
            
            // İşlemleri onayla
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            // Hata durumunda geri al
            DB::rollBack();
            \Log::error('Kullanıcı silinirken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'waiter', 'kitchen', 'cashier', 'customer'])],
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;
        
        try {
            // Transaction başlat
            DB::beginTransaction();
            
            // Kullanıcının rolü customer iken başka bir role değiştiriliyorsa
            if ($oldRole === 'customer' && $newRole !== 'customer') {
                // Customer tablosundaki kaydını bul ve sil
                $customer = Customer::where('user_id', $user->id)->first();
                if ($customer) {
                    // Eğer bu müşterinin siparişleri varsa, siparişleri misafir olarak işaretle
                    $customer->orders()->update(['customer_id' => null]);
                    
                    // Müşteri kaydını sil
                    $customer->delete();
                    
                    \Log::info('Müşteri kaydı silindi: User ID: ' . $user->id);
                }
            }
            // Eğer kullanıcı başka bir rolden customer rolüne değiştiriliyorsa yeni müşteri kaydı oluştur
            elseif ($oldRole !== 'customer' && $newRole === 'customer') {
                // Customer tablosunda kayıt yoksa oluştur
                if (!Customer::where('user_id', $user->id)->exists()) {
                    Customer::create([
                        'user_id' => $user->id
                    ]);
                    
                    \Log::info('Yeni müşteri kaydı oluşturuldu: User ID: ' . $user->id);
                }
            }

            // Kullanıcı rolünü güncelle
            $user->update(['role' => $newRole]);
            
            // İşlemleri onayla
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı rolü başarıyla güncellendi',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Hata durumunda geri al
            DB::rollBack();
            \Log::error('Kullanıcı rolü güncellenirken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı rolü güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
} 