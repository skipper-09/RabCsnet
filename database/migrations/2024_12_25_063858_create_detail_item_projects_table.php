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
        Schema::create('detail_item_projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('detail_id')->references('id')->on('detail_projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity')->default(0);
            $table->decimal('cost_material', 15, 2)->default(0);
            $table->decimal('cost_service', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_item_projects');
    }
};
