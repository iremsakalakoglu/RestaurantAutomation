<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Stok değişim miktarı (+/-)
            $table->enum('type', ['giris', 'cikis']); // giriş mi çıkış mı?
            $table->text('description')->nullable(); // (örn: "10 kg un eklendi")
            $table->decimal('purchase_price', 10, 2)->nullable(); // Alış fiyatı
            $table->decimal('sale_price', 10, 2)->nullable(); // Satış fiyatı
            $table->timestamp('arrival_date')->nullable(); // Stok geliş tarihi
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
