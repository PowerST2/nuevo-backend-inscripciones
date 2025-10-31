<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'diego',
                'email' => 'diegostd99@gmail.com',
                'password' => 'lokiroxd99', 
            ],
            [
                'name' => 'luismayta',
                'email' => 'luis.mayta@gmail.com',
                'password' => '41887192',
            ]
          
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
