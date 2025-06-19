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
            "name" => "Miguel",
            "last_name" => "Dominguez",
            "email" => "testmail@mail.com",
            "username" => "mk2312",
            "password" => bcrypt("Admin247839"),
            "bio" => "Soy un desarrollador web con experiencia en Laravel y React. Me apasiona crear aplicaciones web eficientes y escalables.",
            "profile_picture" => "default.png",
            "role" => User::ROLE_ADMIN,
            "active" => true,
        ]);

        User::factory()->count(1)->create([
            "role" => User::ROLE_USER,
            "username" => "user_" . fake()->unique()->userName(),
            "password" => bcrypt("12345678"),
            "active" => true,
        ]);
    }
}
