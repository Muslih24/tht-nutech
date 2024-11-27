<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'uuid' => Str::uuid()->toString(),
            'nama' => 'Alat Olahraga',
            'created_by' => 'Seeder'

        ]);
        Category::create([
            'uuid' => Str::uuid()->toString(),
            'nama' => 'Alat Musik',
            'created_by' => 'Seeder'
        ]);
    }
}
