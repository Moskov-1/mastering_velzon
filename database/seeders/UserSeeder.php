<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name"=> "admin",
            "email"=> "admin@gmail.com",
            "password" => bcrypt(env("DEFAULT_PASSWORD", '12345678')),
            "role"=> env('DEFAULT_ADMIN_ROLE','admin'),
            "is_admin_user"=> 1,
        ]);

        User::create([
            "name"=> "vendor",
            "email"=> "vendor@gmail.com",
            "password" => bcrypt(env("DEFAULT_PASSWORD", '12345678')),
            "role"=> env('DEFAULT_ADMIN_ROLE','vendor'),
            "is_admin_user"=> 1,
        ]);

        User::create([
            "name"=> "user",
            "email"=> "user@gmail.com",
            "password" => bcrypt(env("DEFAULT_PASSWORD", '12345678')),
            "role"=> env('DEFAULT_USER_ROLE','user'),
        ]);
    }
}
