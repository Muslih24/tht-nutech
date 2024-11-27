<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'uuid' => Str::uuid()->toString(),
            'nama' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'password' => bcrypt('superadmin!@#'),
            'role_id' => '1',
            'created_by' => 'Seeder'
        ]);
        User::create([
            'uuid' => Str::uuid()->toString(),
            'nama' => 'Kristanto',
            'email' => 'kristanto@mail.com',
            'password' => bcrypt('kristanto!@#'),
            'role_id' => '2',
            'created_by' => 'Seeder'
        ]);
    }
}
