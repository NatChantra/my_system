<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('stock_transactions')) return;

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id('txn_id');
            $table->unsignedBigInteger('res_id');
            $table->unsignedBigInteger('sup_id')->nullable();
            $table->enum('type', ['in', 'out']);
            $table->integer('qty');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('note')->nullable();
            $table->timestamp('txn_date')->useCurrent();
            $table->timestamps();

            $table->foreign('res_id')->references('res_id')->on('resources')->onDelete('cascade');
            $table->foreign('sup_id')->references('sup_id')->on('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
