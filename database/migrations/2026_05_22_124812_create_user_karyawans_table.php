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
        Schema::create('user_karyawans', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama', 100);
            $table->enum('role', ['Owner', 'Kasir']);
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_karyawans');
    }
};