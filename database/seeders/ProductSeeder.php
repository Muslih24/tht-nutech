<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'uuid' => Str::uuid()->toString(),
            'user_id' => 2,
            'nama' => 'Bola Basket',
            'harga_beli' => 100000,
            'harga_jual' => 130000,
            'stok' => 10,
            'category_id' => 1,
            'created_by' => 'Seeder',
        ]);
        Product::create([
            'uuid' => Str::uuid()->toString(),
            'user_id' => 2,
            'nama' => 'Gitar Akustik',
            'harga_beli' => 150000,
            'harga_jual' => 200000,
            'stok' => 10,
            'category_id' => 2,
            'created_by' => 'Seeder',
        ]);
    }
}
