<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            
            // Modify the 'subject' and 'causer' to support UUID
            $table->uuid('subject_id')->nullable(); // Use UUID for subject_id
            $table->string('subject_type')->nullable(); // Morphs type for subject

            $table->uuid('causer_id')->nullable(); // Use UUID for causer_id
            $table->string('causer_type')->nullable(); // Morphs type for causer
            
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index('log_name');
        });
    }

    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
}
