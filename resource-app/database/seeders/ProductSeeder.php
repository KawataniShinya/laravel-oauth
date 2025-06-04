<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'ノートパソコン',
                'price' => 120000,
                'description' => '高性能なノートパソコンです。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ワイヤレスマウス',
                'price' => 2500,
                'description' => '使いやすいワイヤレスマウス。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'モニター',
                'price' => 30000,
                'description' => '27インチの大画面モニター。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
