<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['nohp' => '089619060672'],
            [
                'nama' => 'Admin Daurah',
                'nohp' => '089619060672',
                'email' => 'admin@daurah.syathiby.id',
                'password' => null,
                'alamat' => 'Jakarta',
                'lembaga' => 'Daurah Syariyyah',
                'role' => 'admin',
                'domisili' => 'Jakarta',
                'menginap' => 'tidak',
                'agreement_accepted_at' => now(),
            ]
        );
    }
}
