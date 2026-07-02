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
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->increments('pos_id');
                $table->string('pos_name', 255);
                $table->unsignedInteger('dept_id')->nullable();
                $table->timestamps();
            });
        }
    }
};
