<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint \$table) {
            \$table->id('task_id');
            \$table->unsignedBigInteger('emp_id')->nullable();
            \$table->string('task_name');
            \$table->text('description')->nullable();
            \$table->date('deadline')->nullable();
            \$table->string('status')->default('To Do');
            \$table->timestamps();
            \$table->foreign('emp_id')->references('emp_id')->on('employee')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
"@

$file = Get-ChildItem C:\my_system\database\migrations\ | Where-Object { $_.Name -like "*create_tasks*" }
$content | Set-Content $file.FullName
Write-Host "Done: $($file.Name)"