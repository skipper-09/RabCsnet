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
        Schema::create('project_type_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('type_id')->references('id')->on('project_types')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_type_details');
    }
};
