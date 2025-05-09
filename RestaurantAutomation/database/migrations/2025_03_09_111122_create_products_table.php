<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00); // Fiyat kolonu: 10 basamak, 2 ondalık
            $table->string('barcode')->nullable()->unique(); // Barkod alanı
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }

};
