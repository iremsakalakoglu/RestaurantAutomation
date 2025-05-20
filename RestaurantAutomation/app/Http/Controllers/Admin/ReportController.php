<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function incomeExpense(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $months = [
            'Ocak' => 1, 'Şubat' => 2, 'Mart' => 3, 'Nisan' => 4, 'Mayıs' => 5, 'Haziran' => 6,
            'Temmuz' => 7, 'Ağustos' => 8, 'Eylül' => 9, 'Ekim' => 10, 'Kasım' => 11, 'Aralık' => 12
        ];
        $monthNumber = $months[$month] ?? 1;

        // GELİR: Satış (stok çıkışları)
        $income = DB::table('stock_movements')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->where('type', 'cikis')
            ->select(DB::raw('SUM(sale_price * quantity) as total'))
            ->value('total') ?? 0;

        // GİDER: Alış (stok girişleri)
        $expense = DB::table('stock_movements')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->where('type', 'giris')
            ->select(DB::raw('SUM(purchase_price * quantity) as total'))
            ->value('total') ?? 0;

        $net = $income - $expense;

        // Detaylı tablo için veriler
        $details = DB::table('stock_movements')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'income' => number_format($income, 2, ',', '.'),
            'expense' => number_format($expense, 2, ',', '.'),
            'net' => number_format($net, 2, ',', '.'),
            'details' => $details,
        ]);
    }
} 