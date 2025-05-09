<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\TablesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Waiter\WaiterController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\OrderHistoryController;

// Ana Sayfa
Route::get('/', function () {
    return view('homepage');
});

// Menü Sayfası
Route::get('/menu', [MenuController::class, 'index'])->name('menu');

// Order Routes
Route::get('/order', [OrderController::class, 'orderPage'])->name('order.page');
Route::post('/order/store', [OrderController::class, 'storeOrder'])->name('order.store');

// API Routes for admin operations
Route::prefix('api')->group(function() {
    Route::get('/orders/{order}', [App\Http\Controllers\Api\OrderController::class, 'show']);
    Route::put('/orders/{order}', [App\Http\Controllers\Api\OrderController::class, 'update']);
});

// QR Code Routes
Route::get('/qrcode/{tableId}', [QrCodeController::class, 'generateQr'])->name('qrcode.generate');

// Kitchen Routes
Route::prefix('kitchen')->group(function () {
    Route::get('/', [KitchenController::class, 'index'])->name('kitchen.dashboard');
    Route::post('/orders/{order}/status', [KitchenController::class, 'updateOrderStatus'])->name('kitchen.order.status');
    Route::post('/orders/{order}/items/{item}/status', [KitchenController::class, 'updateItemStatus'])->name('kitchen.item.status');
});

// Waiter Routes
Route::prefix('waiter')->group(function () {
    Route::get('/', [WaiterController::class, 'index'])->name('waiter.dashboard');
    Route::get('/orders', [WaiterController::class, 'orders'])->name('waiter.orders');
    Route::get('/order/{order}', [WaiterController::class, 'showOrder'])->name('waiter.order.show');
    Route::post('/order/{order}/status', [WaiterController::class, 'updateOrderStatus'])->name('waiter.order.status');
    Route::post('/order-detail/{id}/delivery', [WaiterController::class, 'updateOrderDetailDelivery'])->name('waiter.orders.update.delivery');
    Route::post('/order/{order}/close', [WaiterController::class, 'closeOrder'])->name('waiter.order.close');
    Route::post('/table/{table}/clear', [WaiterController::class, 'clearTable'])->name('waiter.table.clear');
    Route::post('/order-detail/{id}/cancel', [WaiterController::class, 'cancelOrderDetail'])->name('waiter.order-detail.cancel');
    Route::post('/orders/{id}/update/delivery', [WaiterController::class, 'updateOrderDetailDelivery'])->name('waiter.orders.update.delivery');
    Route::post('/order-detail/cancel/{id}', [WaiterController::class, 'cancelOrderDetail'])->name('waiter.order-detail.cancel');
    Route::post('/orders/{id}/add-product', [WaiterController::class, 'addProductToOrder'])->name('waiter.orders.add-product');
    Route::post('/orders/create', [\App\Http\Controllers\Waiter\WaiterController::class, 'createOrder'])->name('waiter.orders.create');
});

// Cashier Routes
Route::prefix('cashier')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('cashier.dashboard');
    Route::get('/orders', [CashierController::class, 'orders'])->name('cashier.orders');
    Route::get('/order/{order}', [CashierController::class, 'showOrder'])->name('cashier.order.show');
    Route::post('/order/{order}/status', [CashierController::class, 'updateOrderStatus'])->name('cashier.order.status');
    Route::get('/adisyon/{tableId}', [\App\Http\Controllers\Cashier\CashierController::class, 'adisyon'])->name('cashier.adisyon');
    Route::post('/pay-detail/{detailId}', [\App\Http\Controllers\Cashier\CashierController::class, 'payOrderDetail'])->name('cashier.pay-detail');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('admin.categories.show');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    
    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Tables
    Route::get('/tables', [TablesController::class, 'index'])->name('admin.tables');
    Route::post('/tables', [TablesController::class, 'store'])->name('admin.tables.store');
    Route::get('/tables/{id}', [TablesController::class, 'show'])->name('admin.tables.show');
    Route::put('/tables/{id}', [TablesController::class, 'update'])->name('admin.tables.update');
    Route::delete('/tables/{id}', [TablesController::class, 'destroy'])->name('admin.tables.destroy');
    Route::put('/tables/{id}/status', [TablesController::class, 'updateStatus'])->name('admin.tables.status');
    Route::get('/tables/{id}/qrcode', [TablesController::class, 'showQrCode'])->name('admin.tables.qrcode');

    // Orders
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'update'])->name('admin.orders.update');

    // Users
    Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}', [UsersController::class, 'show'])->name('admin.users.show');
    Route::post('/users', [UsersController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [UsersController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('admin.users.destroy');
    Route::put('/users/{id}/role', [UsersController::class, 'updateRole'])->name('admin.users.role.update');

    // Inventory
    Route::get('/inventory', [StockController::class, 'index'])->name('admin.inventory');
    Route::get('/inventory/movements', [StockController::class, 'getAllStockMovements'])->name('admin.inventory.all-movements');
    Route::get('/inventory/products', [StockController::class, 'getProducts'])->name('admin.inventory.products');
    Route::post('/inventory/stock', [StockController::class, 'store'])->name('admin.inventory.store');
    Route::put('/inventory/{stockId}/stock', [StockController::class, 'updateStock'])->name('admin.inventory.update-stock');
    Route::get('/inventory/{stockId}/movements', [StockController::class, 'getStockMovements'])->name('admin.inventory.movements');
    Route::get('/inventory/barcode/{barcode}', [StockController::class, 'searchByBarcode'])->name('admin.inventory.barcode');

    // Tedarikçi Yönetimi (Supplier)
    Route::get('/suppliers', [StockController::class, 'getSuppliers'])->name('admin.suppliers.index');
    Route::get('/suppliers/{id}', [StockController::class, 'getSupplier'])->name('admin.suppliers.show');
    Route::post('/suppliers', [StockController::class, 'storeSupplier'])->name('admin.suppliers.store');
    Route::put('/suppliers/{id}', [StockController::class, 'updateSupplier'])->name('admin.suppliers.update');
    Route::delete('/suppliers/{id}', [StockController::class, 'deleteSupplier'])->name('admin.suppliers.delete');

    // Üretici Yönetimi
    Route::get('/manufacturers', [StockController::class, 'getManufacturers'])->name('admin.manufacturers.index');
    Route::get('/manufacturers/{manufacturer}', [StockController::class, 'getManufacturer'])->name('admin.manufacturers.show');
    Route::post('/manufacturers', [StockController::class, 'storeManufacturer'])->name('admin.manufacturers.store');
    Route::put('/manufacturers/{manufacturer}', [StockController::class, 'updateManufacturer'])->name('admin.manufacturers.update');
    Route::delete('/manufacturers/{manufacturer}', [StockController::class, 'deleteManufacturer'])->name('admin.manufacturers.delete');
    Route::post('/manufacturers/reload-defaults', [StockController::class, 'reloadDefaultManufacturers'])->name('admin.manufacturers.reload-defaults');

    // Tedarikçi güncelleme (stok için)
    Route::post('/stocks/{id}/update-supplier', [StockController::class, 'updateSupplierForStock']);

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'showSettingsPage'])->name('admin.settings');
    Route::post('/settings/general', [\App\Http\Controllers\SettingsController::class, 'saveGeneral'])->name('admin.settings.general');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Cart Routes
Route::get('/cart', function () {
    return view('cart');
})->name('cart');

Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');

// Hesap Bilgilerim (Kullanıcı Profili)
Route::middleware(['auth'])->get('/account-info', function () {
    return view('accountinfo');
})->name('account.info');

Route::middleware(['auth'])->post('/account-info/update', [App\Http\Controllers\AuthController::class, 'updateAccountInfo'])->name('account.info.update');
Route::middleware(['auth'])->post('/account-info/password-update', [App\Http\Controllers\AuthController::class, 'updatePassword'])->name('account.password.update');

// Sipariş Geçmişi
Route::middleware(['auth'])->get('/orderhistory', [OrderHistoryController::class, 'index'])->name('orderhistory');
Route::middleware(['auth'])->get('/order-history', [OrderHistoryController::class, 'index'])->name('order.history');
Route::middleware(['auth'])->get('/order-history/{id}', [OrderHistoryController::class, 'show'])->name('order.history.detail');
Route::middleware(['auth'])->get('/favorites', [OrderHistoryController::class, 'favorites'])->name('favorites');
Route::middleware(['auth'])->get('/notifications', [OrderHistoryController::class, 'notifications'])->name('notifications');

Route::middleware(['auth'])->post('/order/{id}/repeat', [App\Http\Controllers\OrderController::class, 'repeatOrder'])->name('order.repeat');

