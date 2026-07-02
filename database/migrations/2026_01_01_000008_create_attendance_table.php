<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('att_id');
            $table->unsignedBigInteger('emp_id');
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('gps_location')->nullable();
            $table->string('device_id')->nullable();
            $table->string('scan_token')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('emp_id')->references('emp_id')->on('employee')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};