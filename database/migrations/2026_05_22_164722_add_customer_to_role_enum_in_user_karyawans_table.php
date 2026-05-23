<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE user_karyawans MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'Kasir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE user_karyawans MODIFY COLUMN role ENUM('Owner', 'Kasir') NOT NULL DEFAULT 'Kasir'");
    }
};