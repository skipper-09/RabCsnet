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
        Schema::create('task_assigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->references('id')->on('tasks')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('vendor_id')->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->date('finish_date');
            $table->integer('progress');
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assigns');
    }
};