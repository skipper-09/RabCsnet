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
        Schema::create('payment_vendors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('vendor_id')->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('amount',10,2)->default(0);
            $table->date('payment_date');
            $table->longText('note');
            $table->longText('bukti_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vendors');
    }
};
