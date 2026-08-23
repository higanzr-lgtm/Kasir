<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('status_pengiriman', 20)->default('menunggu')->after('nomor_hp');
            $table->text('detail_alamat')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['status_pengiriman', 'detail_alamat']);
        });
    }
};