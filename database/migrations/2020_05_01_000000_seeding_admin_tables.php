<?php

use Faker\Factory;
use Illuminate\Database\Migrations\Migration;

class SeedingAdminTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', ['--class' => 'AdminTablesSeeder', '--force' => true]);

        DB::table('admin_users')->truncate();
        DB::table('admin_users')->insert(
            [
                [
                    "id" => 1,
                    "username" => "developer",
                    "password" => bcrypt("developer"),
                    "name" => "開発者",
                ],
                [
                    "id" => 2,
                    "username" => "admin",
                    "password" => bcrypt("admin"),
                    "name" => "管理者",
                ]
            ]
        );

        DB::table('admin_role_users')->truncate();
        DB::table('admin_role_users')->insert(
            [
                [
                    "role_id" => 1, // 開発者ロール
                    "user_id" => 1
                ],
                [
                    "role_id" => 2, // 管理ユーザーロール
                    "user_id" => 2
                ]
            ]
        );

        DB::table('admin_user_permissions')->truncate();
        DB::table('admin_user_permissions')->insert(
            [

            ]
        );
    }
}
