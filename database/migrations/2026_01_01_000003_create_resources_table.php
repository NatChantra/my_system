<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id('res_id');
            $table->string('res_name');
            $table->unsignedBigInteger('cat_id')->nullable();
            $table->unsignedBigInteger('sup_id')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock_qty')->default(0);
            $table->integer('low_stock_alert')->default(10);
            $table->string('unit')->default('pcs');
            $table->timestamps();

            $table->foreign('cat_id')->references('cat_id')->on('categories')->nullOnDelete();
            $table->foreign('sup_id')->references('sup_id')->on('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};