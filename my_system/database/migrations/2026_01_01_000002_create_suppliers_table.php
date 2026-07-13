<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('suppliers')) return;

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('sup_id');
            $table->string('sup_name');
            $table->string('sup_phone')->nullable();
            $table->string('sup_email')->nullable();
            $table->string('sup_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
