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
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('unit_id');
            $table->string('item_code')->unique()->nullable(false);
            $table->string('material_price');
            $table->string('service_price');
            $table->longText('description');
            $table->foreign('type_id')->references('id')->on('type_items')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
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
