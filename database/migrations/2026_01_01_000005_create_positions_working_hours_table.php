<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Positions table
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id('pos_id');
                $table->string('pos_name');
                $table->unsignedInteger('dept_id')->nullable();
                $table->foreign('dept_id')->references('dept_id')->on('departments')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Working Hours table
        if (!Schema::hasTable('working_hours')) {
            Schema::create('working_hours', function (Blueprint $table) {
                $table->id('wh_id');
                $table->unsignedInteger('dept_id')->nullable();
                $table->foreign('dept_id')->references('dept_id')->on('departments')->onDelete('cascade');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('days')->default('Mon-Fri'); // ឧ. Mon-Fri, Mon-Sat
                $table->string('note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('working_hours');
        Schema::dropIfExists('positions');
    }
};