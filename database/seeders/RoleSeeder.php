<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'uuid' => Str::uuid()->toString(),
            'nama' => 'Super Admin',
            'created_by' => 'Seeder'
        ]);
        Role::create([
            'uuid' => Str::uuid()->toString(),
            'nama' => 'Website Programmer',
            'created_by' => 'Seeder'
        ]);
    }
}
