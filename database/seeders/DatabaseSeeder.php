<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        DB::table('roles')->insert([
            [
                'role' => 'admin', 
            ], 
            [
                'role' => 'staff', 
            ],
            [
                'role' => 'supplier', 
            ], 
        ]);

        DB::table('users')->insert([
            [
                'role_id' => '1', // 1 = admin
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('User123'),
            ], 
            [
                'role_id' => '2',// 2 = staff
                'first_name' => 'Jane',
                'last_name' => 'Mat',
                'email' => 'staff@gmail.com',
                'password' => bcrypt('User123'),
            ],
            [
                'role_id' => '3', // 3 = supplier
                'first_name' => 'Sanny',
                'last_name' => 'Bam',
                'email' => 'supplier@gmail.com',
                'password' => bcrypt('User123'),
            ]
        ]);

        DB::table('system_settings')->insert([
            [
                'meta_field' => 'title', 
                'meta_value' => 'Pharmacy Inventory System', 
            ], 
            [
                'meta_field' => 'name', 
                'meta_value' => 'Pharmims', 
            ], 
            [
                'meta_field' => 'logo_url', 
                'meta_value' => 'defaults/pharmims-logo.png', 
            ],
            [
                'meta_field' => 'logo_lg_url', 
                'meta_value' => 'defaults/pharmims-logo-lg.png', 
            ],
            [
                'meta_field' => 'cover_url', 
                'meta_value' => 'defaults/cover.svg', 
            ],
             [
                'meta_field' => 'icon_url', 
                'meta_value' => '', 
            ],
            [
                'meta_field' => 'currency', 
                'meta_value' => '₱', 
            ],
        ]);

        DB::table('categories')->insert([
            'category' => 'Medicine'
        ]);
    }
}
// php artisan make:controller CartController --model=Cart --api 
// 'email' => admin@gmail.com
// 'password' => User123
// 'email' => staff@gmail.com
// 'password' => User123
// 'email' => supplier@gmail.com
// 'password' => User123