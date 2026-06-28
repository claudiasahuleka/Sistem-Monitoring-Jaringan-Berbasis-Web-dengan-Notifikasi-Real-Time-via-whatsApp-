<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            ['name' => 'Router Utama',     'ip_address' => '192.168.1.1',  'location' => 'Ruang Server Lt.1', 'type' => 'router'],
            ['name' => 'Switch Lantai 1',  'ip_address' => '192.168.1.2',  'location' => 'Lantai 1 Gedung A', 'type' => 'switch'],
            ['name' => 'Switch Lantai 2',  'ip_address' => '192.168.1.3',  'location' => 'Lantai 2 Gedung A', 'type' => 'switch'],
            ['name' => 'Server File',      'ip_address' => '192.168.1.10', 'location' => 'Ruang Server Lt.1', 'type' => 'server'],
            ['name' => 'AP Lobby',         'ip_address' => '192.168.1.20', 'location' => 'Lobby Gedung A',    'type' => 'access_point'],
        ];

        foreach ($devices as $d) {
            Device::create(array_merge($d, ['is_active' => true]));
        }

        $this->command->info('✓ ' . count($devices) . ' perangkat ditambahkan.');
    }
}