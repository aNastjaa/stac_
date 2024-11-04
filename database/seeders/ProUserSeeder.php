<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ProUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Ensure the 'pro' role exists
        $proRole = Role::firstOrCreate(
            ['name' => 'pro'],
            ['description' => 'Pro user with special privileges']
        );

        // Create a pro user
        User::create([
            'username' => 'Pro User',
            'email' => 'prouser@gmail.com',
            'password' => bcrypt('ProUserPassword2024'),
            'role_id' => $proRole->id,
        ]);
    }
}
