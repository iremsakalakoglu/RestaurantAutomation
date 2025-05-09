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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); //ürün
            $table->enum('unit', ['adet', 'kg', 'lt', 'gr'])->default('adet'); //ölçü
            $table->integer('quantity')->default(0); //stok miktar
            $table->string('supplier')->nullable(); //tedarikçi
            $table->string('manufacturer')->nullable(); //üretici
            $table->decimal('purchase_price', 10, 2)->nullable(); //alış fiyatı
            $table->decimal('sale_price', 10, 2)->nullable(); //satış fiyatı
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
