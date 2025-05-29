<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'manager',
                'email' => 'manager@test.com',
                'password' => Hash::make('password123'),
                'role_id' =>
                    DB::table('roles')->where('name', Role::ADMIN->value)->value('id'),
            ],
            [
                'name' => 'staff',
                'email' => 'staff@test.com',
                'password' => Hash::make('password123'),
                'role_id' =>
                    DB::table('roles')->where('name', Role::STAFF->value)->value('id'),
            ],
        ]);
    }
}
