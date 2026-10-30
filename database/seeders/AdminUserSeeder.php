<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Support both exact string 'admin@admiin.com' and standard 'admin@admin.com'
        $emails = [
            'admin@admiin.com',
            'admin@admin.com',
        ];

        foreach ($emails as $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'AURA Store Administrator',
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
