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
        $roleIds = DB::table('roles')->pluck('id', 'name');
        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        // 管理者に全パーミッション
        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $roleIds[Role::ADMIN->value],
                'permission_id' => $permissionId,
            ]);
        }

        // スタッフは read のみ
        if (isset($permissionIds['read'])) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $roleIds[Role::STAFF->value],
                'permission_id' => $permissionIds[Permission::READ->value],
            ]);
        }
    }
}
