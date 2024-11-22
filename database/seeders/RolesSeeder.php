<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'Admin',
                'login_start_time' => '10:00:00',
                'login_end_time' => '19:00:00',
                'is_all_time_login' => 1,
            ],
            [
                'name' => 'Hr',
                'login_start_time' => '10:00:00',
                'login_end_time' => '19:00:00',
                'is_all_time_login' => 0,
            ],
            [
                'name' => 'backend_team',
                'login_start_time' => '10:00:00',
                'login_end_time' => '19:00:00',
                'is_all_time_login' => 0,
            ],
            [
                'name' => 'backgroud_team',
                'login_start_time' => '10:00:00',
                'login_end_time' => '19:00:00',
                'is_all_time_login' => 0,
            ],
            [
                'name' => 'feild_team',
                'login_start_time' => '10:00:00',
                'login_end_time' => '19:00:00',
                'is_all_time_login' => 0,
            ],
        ]);
    }
}
