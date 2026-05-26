<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Buat akun admin default untuk testing.
     *
     * Login: username = admin  |  email = admin@sikema.sch.id
     * Password: admin123
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'email'    => 'admin@sikema.sch.id',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
