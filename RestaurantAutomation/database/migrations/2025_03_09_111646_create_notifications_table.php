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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade'); // misafir girişi için nullable
            $table->enum('type', ['sipariş', 'ödeme', 'stok', 'genel'])->default('genel'); // bildirim türü
            $table->string('message', 255); // karakter sınırı
            $table->enum('status', ['okundu', 'okunmadı'])->default('okunmadı');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
