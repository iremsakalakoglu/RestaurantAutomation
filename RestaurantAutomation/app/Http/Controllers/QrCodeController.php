<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Table;

class QrCodeController extends Controller
{
    public function generateQr($tableId)
    {
        $table = Table::where('id', $tableId)->orWhere('table_number', $tableId)->firstOrFail();
        $baseUrl = config('app.url');
        $url = $baseUrl . '/menu?table=' . $table->id;
        return response(QrCode::size(300)->generate($url))
            ->header('Content-Type', 'image/svg+xml');
    }
}
