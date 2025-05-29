<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\Scope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleIds = DB::table('roles')->pluck('id', 'name');
        $scopeIds = DB::table('scopes')->pluck('id', 'name');

        // 管理者は全てのスコープにアクセス
        foreach ($scopeIds as $scopeId) {
            DB::table('role_scope')->insertOrIgnore([
                'role_id' => $roleIds[Role::ADMIN->value],
                'scope_id' => $scopeId,
            ]);
        }

        // スタッフは general のみ
        if (isset($scopeIds[Scope::GENERAL->value])) {
            DB::table('role_scope')->insertOrIgnore([
                'role_id' => $roleIds[Role::STAFF->value],
                'scope_id' => $scopeIds[Scope::GENERAL->value],
            ]);
        }
    }
}
