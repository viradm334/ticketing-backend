<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);

        $superadmin->assignRole('superadmin');

        // admin users
        $admins = User::factory(5)->create();

        foreach($admins as $admin){
            $admin->assignRole('agent');
        }

        // users
        $users = User::factory(5)->create();

        foreach($users as $user){
            $user->assignRole('user');
        }
    }
}
