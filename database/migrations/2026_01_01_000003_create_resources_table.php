<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('resources')) {
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
        } else {
            Schema::table('resources', function (Blueprint $table) {
                if (!Schema::hasColumn('resources', 'cat_id')) {
                    $table->unsignedBigInteger('cat_id')->nullable()->after('res_name');
                    $table->foreign('cat_id')->references('cat_id')->on('categories')->nullOnDelete();
                }
                if (!Schema::hasColumn('resources', 'sup_id')) {
                    $table->unsignedBigInteger('sup_id')->nullable()->after('cat_id');
                    $table->foreign('sup_id')->references('sup_id')->on('suppliers')->nullOnDelete();
                }
                if (!Schema::hasColumn('resources', 'price')) {
                    $table->decimal('price', 10, 2)->default(0)->after('sup_id');
                }
                if (!Schema::hasColumn('resources', 'low_stock_alert')) {
                    $table->integer('low_stock_alert')->default(10)->after('stock_qty');
                }
                if (!Schema::hasColumn('resources', 'unit')) {
                    $table->string('unit')->default('pcs')->after('low_stock_alert');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};