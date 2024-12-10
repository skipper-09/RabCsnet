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
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('type_id')->references('id')->on('type_items')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
            $table->string('item_code')->unique()->nullable(false);
            $table->string('material_price')->nullable();
            $table->string('service_price');
            $table->longText('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
