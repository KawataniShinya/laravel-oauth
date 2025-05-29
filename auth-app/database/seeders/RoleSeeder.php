<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Role::cases() as $role) {
            DB::table('roles')->insertOrIgnore([
                'name' => $role->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
