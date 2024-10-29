<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('AdminPassword2024'),
            'role_id' => DB::table('roles')->where('name', 'admin')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
