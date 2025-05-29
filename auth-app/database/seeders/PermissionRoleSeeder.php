<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者に全パーミッション
        foreach (Permission::cases() as $permission) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => Role::ADMIN->id(),
                'permission_id' => $permission->id(),
            ]);
        }

        // スタッフは read のみ
        DB::table('permission_role')->insertOrIgnore([
            'role_id' => Role::STAFF->id(),
            'permission_id' => Permission::READ->id(),
        ]);
    }
}
