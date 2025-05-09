<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Str;

class TablesController extends Controller
{
    public function index()
    {
        $tables = Table::with('waiter')->orderBy('table_number', 'asc')->paginate(8);
        $waiters = User::where('role', 'waiter')->get();
        return view('Dashboard.tables', compact('tables', 'waiters'));
    }

    public function show($id)
    {
        $table = Table::with('waiter')->findOrFail($id);
        return response()->json($table);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables',
            'capacity' => 'required|integer|min:1',
            'waiter_id' => 'nullable|exists:users,id'
        ]);

        $table = new Table();
        $table->table_number = $request->table_number;
        $table->capacity = $request->capacity;
        $table->qr_code = Str::random(10);
        $table->status = 'boş';
        $table->waiter_id = $request->waiter_id;
        $table->save();

        return response()->json([
            'success' => true, 
            'message' => 'Masa başarıyla eklendi',
            'table' => $table,
            'qr_url' => url('/qrcode/' . $table->id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'table_number' => 'required|unique:tables,table_number,' . $id,
            'status' => 'nullable|in:boş,dolu',
            'capacity' => 'required|integer|min:1',
            'waiter_id' => 'nullable|exists:users,id'
        ]);

        $table = Table::findOrFail($id);
        $table->table_number = $request->table_number;
        $table->capacity = $request->capacity;
        
        if ($request->has('status')) {
            $table->status = $request->status;
        }

        if ($request->has('waiter_id')) {
            $table->waiter_id = $request->waiter_id;
        }
        
        $table->save();

        return response()->json(['success' => true, 'message' => 'Masa başarıyla güncellendi']);
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return response()->json(['success' => true, 'message' => 'Masa başarıyla silindi']);
    }

    public function updateStatus(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $table->status = $request->status;
        $table->save();

        return response()->json(['success' => true, 'message' => 'Masa durumu güncellendi']);
    }
    
    public function showQrCode($id)
    {
        $table = Table::findOrFail($id);
        return view('Dashboard.qrcode', compact('table'));
    }
} 