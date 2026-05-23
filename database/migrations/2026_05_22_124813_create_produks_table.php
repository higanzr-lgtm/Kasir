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
        Schema::create('produks', function (Blueprint $table) {
            $table->string('id_produk', 50)->primary();
            $table->string('id_diskon', 50)->nullable();
            $table->string('nama_produk', 150);
            $table->decimal('harga_normal', 10, 2);
            $table->integer('stok')->default(0);
            $table->timestamps();

            // Relasi ke tabel diskon (Sesuai ERD)
            $table->foreign('id_diskon')->references('id_diskon')->on('diskons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};