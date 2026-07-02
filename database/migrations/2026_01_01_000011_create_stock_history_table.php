<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_history', function (Blueprint $table) {
            $table->id('history_id');
            $table->unsignedBigInteger('res_id');
            $table->enum('type', ['in', 'out', 'edit', 'create']);
            $table->integer('qty_change');
            $table->integer('qty_before');
            $table->integer('qty_after');
            $table->string('note')->nullable();
            $table->unsignedBigInteger('emp_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_history');
    }
};