<?php

namespace Database\Seeders;

use App\Enums\Scope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Scope::cases() as $scope) {
            DB::table('scopes')->insertOrIgnore([
                'id' => $scope->id(),
                'name' => $scope->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
