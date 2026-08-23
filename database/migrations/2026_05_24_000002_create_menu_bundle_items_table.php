<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bundle_id');
            $table->string('id_produk');
            $table->integer('qty')->default(1);
            $table->timestamps();

            $table->foreign('bundle_id')->references('id')->on('menu_bundles')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_bundle_items');
    }
};