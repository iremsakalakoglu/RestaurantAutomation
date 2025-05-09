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
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Ürünler tablosuna manufacturer_id ekle
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('manufacturer_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
        });

        // Stok hareketleri tablosundaki manufacturer alanını kaldır
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('manufacturer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Önce foreign key'i kaldır
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['manufacturer_id']);
            $table->dropColumn('manufacturer_id');
        });

        // Stok tablosuna manufacturer kolonunu geri ekle
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('manufacturer')->nullable();
        });

        // Manufacturers tablosunu kaldır
        Schema::dropIfExists('manufacturers');
    }
};
