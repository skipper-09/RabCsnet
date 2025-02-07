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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('responsible_person')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('vendor_id')->nullable()->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->longText('description');
            $table->decimal('amount',15,2)->default(0)->nullable();
            $table->enum('status', ['pending', 'in_progres', 'finish', 'canceled'])->default('pending');
            // $table->enum('status_pengajuan', ['pending', 'in_review', 'approved','rejected','revision'])->default('pending');
            $table->boolean('start_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
