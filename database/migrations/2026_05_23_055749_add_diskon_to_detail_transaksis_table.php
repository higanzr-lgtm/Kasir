<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->decimal('diskon_persen', 5, 2)->default(0)->after('subtotal');
            $table->decimal('diskon_nominal', 10, 2)->default(0)->after('diskon_persen');
            $table->decimal('subtotal_setelah_diskon', 10, 2)->default(0)->after('diskon_nominal');
        });
    }

    public function down(): void
    {
        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->dropColumn(['diskon_persen', 'diskon_nominal', 'subtotal_setelah_diskon']);
        });
    }
};