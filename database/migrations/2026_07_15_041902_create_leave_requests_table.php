<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id('leave_id');
            $table->unsignedBigInteger('emp_id')->nullable();
            $table->unsignedBigInteger('leave_type_id')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->foreign('emp_id')->references('emp_id')->on('employee')->onDelete('set null');
            $table->foreign('leave_type_id')->references('leave_type_id')->on('leave_types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};