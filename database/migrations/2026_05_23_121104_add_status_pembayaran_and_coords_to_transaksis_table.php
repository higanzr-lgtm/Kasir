<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('status_pembayaran', 20)->default('Belum Bayar')->after('total_bayar');
            $table->string('latitude', 30)->nullable()->after('detail_alamat');
            $table->string('longitude', 30)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['status_pembayaran', 'latitude', 'longitude']);
        });
    }
};