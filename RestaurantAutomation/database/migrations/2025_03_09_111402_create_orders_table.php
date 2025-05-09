<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('table_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['sipariş alındı', 'hazırlanıyor', 'hazır', 'teslim edildi', 'iptal edildi'])->default('sipariş alındı');
            $table->enum('payment_status', ['bekliyor', 'ödendi', 'iptal edildi'])->default('bekliyor');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }

};
