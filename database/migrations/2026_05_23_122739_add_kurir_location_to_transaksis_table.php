<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('kurir_latitude', 30)->nullable()->after('longitude');
            $table->string('kurir_longitude', 30)->nullable()->after('kurir_latitude');
            $table->timestamp('kurir_updated_at')->nullable()->after('kurir_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['kurir_latitude', 'kurir_longitude', 'kurir_updated_at']);
        });
    }
};