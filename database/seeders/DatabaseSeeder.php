<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('leave_types')->insertOrIgnore([
            ['leave_type_id' => 1, 'type_name' => 'Annual Leave', 'created_at' => now(), 'updated_at' => now()],
            ['leave_type_id' => 2, 'type_name' => 'Sick Leave', 'created_at' => now(), 'updated_at' => now()],
            ['leave_type_id' => 3, 'type_name' => 'Unpaid Leave', 'created_at' => now(), 'updated_at' => now()],
            ['leave_type_id' => 4, 'type_name' => 'Maternity Leave', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}