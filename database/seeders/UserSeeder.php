<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $adminRole = Role::where('name', 'admin')->first();
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create editor user
        $editorRole = Role::where('name', 'editor')->first();
        User::create([
            'name' => 'Editor',
            'email' => 'editor@editor.com',
            'password' => Hash::make('password'),
            'role_id' => $editorRole->id,
        ]);

        // Create wartawan user
        $wartawanRole = Role::where('name', 'wartawan')->first();
        User::create([
            'name' => 'Wartawan',
            'email' => 'wartawan@wartawan.com',
            'password' => Hash::make('password'),
            'role_id' => $wartawanRole->id,
        ]);
    }
} 