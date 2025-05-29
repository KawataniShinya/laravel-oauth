<?php

namespace Database\Seeders;

use App\Enums\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Permission::cases() as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
