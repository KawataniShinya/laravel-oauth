<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customers')->insert([
            [
                'name' => '山田 太郎',
                'email' => 'yamada@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '佐藤 花子',
                'email' => 'sato@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '鈴木 次郎',
                'email' => 'suzuki@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
