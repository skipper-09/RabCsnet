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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('vendor_id')->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('parent_id')->nullable()->references('id')->on('tasks')->onDelete('cascade');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('complated_date')->nullable();
            $table->enum('status',['pending','in_progres','complated','overdue'])->default('pending');
            $table->enum('priority',['low','medium','high'])->default('low');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
