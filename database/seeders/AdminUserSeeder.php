<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@disnakertrans.go.id'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@disnakertrans.go.id',
                'password' => Hash::make('Admin@12345'), // Ganti setelah login!
            ]
        );

        $this->command->info('✅ Akun admin berhasil dibuat!');
        $this->command->info('   Email   : admin@disnakertrans.go.id');
        $this->command->info('   Password: Admin@12345');
        $this->command->warn('   ⚠️  Segera ganti password setelah login pertama!');
    }
}