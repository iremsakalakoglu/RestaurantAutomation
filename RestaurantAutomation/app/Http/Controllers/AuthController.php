<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Giriş formunu gösterir.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Kayıt formunu gösterir.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Kullanıcı giriş işlemi.
     */
    public function login(Request $request)
    {
        // Gelen verileri doğrula
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Beni hatırla özelliği eklemek için:
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Kullanıcı rolüne göre yönlendirme
            if (Auth::user()->role === 'admin') {
                return redirect()->route('dashboard');
            }
            else if (Auth::user()->role === 'kitchen') {
                return redirect()->route('kitchen.dashboard');
            }
            else if (Auth::user()->role === 'waiter') {
                return redirect()->route('waiter.dashboard');
            }
            else if (Auth::user()->role === 'cashier') {
                return redirect()->route('cashier.dashboard');
            }
            return redirect()->intended('menu')->with('success', 'Başarıyla giriş yaptınız!');

    }

        return back()->with('error', 'E-posta veya şifre hatalı!');
    }

    /**
     * Kullanıcı kayıt işlemi.
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'lastName' => 'nullable|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'phone' => 'nullable|string|max:11',
                'password' => 'required|confirmed|min:6',
            ]);

            DB::beginTransaction();

            try {
                // Kullanıcı oluşturuluyor
                $user = User::create([
                    'name' => $request->name,
                    'lastName' => $request->lastName,
                    'email' => $request->email,
                    'phone' => preg_replace('/\D/', '', $request->phone) ?? '00000000000',
                    'password' => Hash::make($request->password),
                    'role' => 'customer',
                ]);

                // Customer kaydı oluştur
                Customer::create([
                    'user_id' => $user->id
                ]);

                DB::commit();

                // Kullanıcıyı giriş yapmış olarak işaretle
                Auth::login($user);

                return redirect()->intended('menu')->with('success', 'Kayıt başarıyla tamamlandı!');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            // Hata mesajını loglayalım
            \Log::error('Kayıt hatası: ' . $e->getMessage());
            return back()->with('error', 'Kayıt işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    /**
     * Kullanıcı çıkış işlemi.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Kullanıcı hesap bilgilerini günceller.
     */
    public function updateAccountInfo(Request $request)
    {
        $user = Auth::user();
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->lastName = $request->lastName;
        $user->email = $request->email;
        $user->phone = preg_replace('/\D/', '', $request->phone);
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bilgileriniz başarıyla güncellendi.'
            ]);
        }
        return redirect()->back()->with('success', 'Bilgileriniz başarıyla güncellendi.');
    }

    /**
     * Kullanıcı şifresini günceller.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Mevcut şifreyi kontrol et
        if (!\Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Mevcut şifreniz yanlış!']);
        }

        // Şifreyi güncelle
        $user->password = \Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Şifreniz başarıyla güncellendi!']);
    }
}
