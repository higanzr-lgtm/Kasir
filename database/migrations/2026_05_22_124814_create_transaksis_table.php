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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->string('id_transaksi', 50)->primary();
            $table->unsignedBigInteger('id_user');
            $table->decimal('total_bayar', 10, 2)->default(0);
            $table->timestamps();

            // Relasi ke tabel user (Kasir yang melayani)
            $table->foreign('id_user')->references('id_user')->on('user_karyawans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};