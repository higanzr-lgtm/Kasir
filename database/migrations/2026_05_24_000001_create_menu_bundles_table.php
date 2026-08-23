<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bundle');
            $table->decimal('harga_bundle', 12, 0);
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_bundles');
    }
};