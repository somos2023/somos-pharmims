<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('users')->insert([
            [
                'role_id' => '1', // 1 = admin
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('User123'),
            ], 
            [
                'role_id' => '2',// 2 = staff
                'first_name' => 'Staff',
                'last_name' => 'Staff',
                'email' => 'xomosstaff@gmail.com',
                'password' => bcrypt('User123'),
            ],
            [
                'role_id' => '3', // 3 = supplier
                'first_name' => 'Supplier',
                'last_name' => 'Supplier',
                'email' => 'xomosadmin@gmail.com',
                'password' => bcrypt('User123'),
            ]
        ]);
    }
}
