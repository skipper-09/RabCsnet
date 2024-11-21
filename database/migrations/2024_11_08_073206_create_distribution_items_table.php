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
        Schema::create('distribution_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->unsignedBigInteger('distribution_id');
            // $table->unsignedBigInteger('item_id');
            $table->integer('material_count');
            $table->foreignUuid('distribution_id')->references('id')->on('distributions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_items');
    }
};
