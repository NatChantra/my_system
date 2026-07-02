<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee')) {
            Schema::create('employee', function (Blueprint $table) {
                $table->id();
                $table->string('emp_name');
                $table->string('photo')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('employee', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('emp_name');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};