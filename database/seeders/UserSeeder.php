<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Örnek: 1 adet Admin kullanıcısı oluşturalım
        User::updateOrCreate(
            ['email' => 'cemalkuller@gmail.com'],
            [
                'name' => 'Cemal Küller',
                'password' => Hash::make('123456'),  // İstediğiniz güçlü bir şifre koyun
                'daily_limit' => 2000,                     // Günlük sorgu limiti
                'role' => 'admin',                  // Admin rolü
            ]
        );

        // Opsiyonel: Yeni bir 'editor' kullanıcı
        User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor Kullanıcı',
                'password' => Hash::make('editor123'),
                'daily_limit' => 500,
                'role' => 'editor',
            ]
        );

        // Opsiyonel: Yeni bir normal 'user'
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal Kullanıcı',
                'password' => Hash::make('user123'),
                'daily_limit' => 100,
                'role' => 'user',
            ]
        );
    }
}
