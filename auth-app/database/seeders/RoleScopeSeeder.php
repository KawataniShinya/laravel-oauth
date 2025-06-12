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
        // 管理者は全てのスコープにアクセス
        foreach (Scope::cases() as $scope) {
            DB::table('role_scope')->insertOrIgnore([
                'role_id' => Role::ADMIN->id(),
                'scope_id' => $scope->id(),
            ]);
        }

        // スタッフは general のみ
        DB::table('role_scope')->insertOrIgnore([
            'role_id' => Role::STAFF->id(),
            'scope_id' => Scope::GENERAL->id(),
        ]);
    }
}
