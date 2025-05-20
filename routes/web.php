// Cashier Routes
Route::prefix('cashier')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('cashier.dashboard');
    Route::get('/orders', [CashierController::class, 'orders'])->name('cashier.orders');
    Route::get('/order/{order}', [CashierController::class, 'showOrder'])->name('cashier.order.show');
    Route::post('/order/{order}/status', [CashierController::class, 'updateOrderStatus'])->name('cashier.order.status');
    Route::get('/adisyon/{tableId}', [\App\Http\Controllers\Cashier\CashierController::class, 'adisyon'])->name('cashier.adisyon');
    Route::post('/pay-detail/{detailId}', [\App\Http\Controllers\Cashier\CashierController::class, 'payOrderDetail'])->name('cashier.pay-detail');
    Route::post('/pay-all/{orderId}', [\App\Http\Controllers\Cashier\CashierController::class, 'payAllOrderDetails'])->name('cashier.pay-all');
}); 