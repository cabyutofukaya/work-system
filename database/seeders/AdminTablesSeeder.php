<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        \Encore\Admin\Auth\Database\Menu::truncate();
        \Encore\Admin\Auth\Database\Menu::insert(
            [
                [
                    "icon" => "fa-home",
                    "order" => 1,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "ホーム",
                    "uri" => "/"
                ],
                [
                    "icon" => "fa-tasks",
                    "order" => 14,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "Admin",
                    "uri" => ""
                ],
                [
                    "icon" => "fa-users",
                    "order" => 15,
                    "parent_id" => 2,
                    "permission" => NULL,
                    "title" => "Users",
                    "uri" => "auth/users"
                ],
                [
                    "icon" => "fa-user",
                    "order" => 16,
                    "parent_id" => 2,
                    "permission" => NULL,
                    "title" => "Roles",
                    "uri" => "auth/roles"
                ],
                [
                    "icon" => "fa-ban",
                    "order" => 17,
                    "parent_id" => 2,
                    "permission" => NULL,
                    "title" => "Permission",
                    "uri" => "auth/permissions"
                ],
                [
                    "icon" => "fa-bars",
                    "order" => 18,
                    "parent_id" => 2,
                    "permission" => NULL,
                    "title" => "Menu",
                    "uri" => "auth/menu"
                ],
                [
                    "icon" => "fa-history",
                    "order" => 19,
                    "parent_id" => 2,
                    "permission" => NULL,
                    "title" => "Operation log",
                    "uri" => "auth/logs"
                ],
                [
                    "icon" => "fa-male",
                    "order" => 11,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "メンバー",
                    "uri" => "users"
                ],
                [
                    "icon" => "fa-gears",
                    "order" => 20,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "Helpers",
                    "uri" => NULL
                ],
                [
                    "icon" => "fa-keyboard-o",
                    "order" => 21,
                    "parent_id" => 9,
                    "permission" => NULL,
                    "title" => "Scaffold",
                    "uri" => "helpers/scaffold"
                ],
                [
                    "icon" => "fa-database",
                    "order" => 22,
                    "parent_id" => 9,
                    "permission" => NULL,
                    "title" => "Database terminal",
                    "uri" => "helpers/terminal/database"
                ],
                [
                    "icon" => "fa-terminal",
                    "order" => 23,
                    "parent_id" => 9,
                    "permission" => NULL,
                    "title" => "Laravel artisan",
                    "uri" => "helpers/terminal/artisan"
                ],
                [
                    "icon" => "fa-list-alt",
                    "order" => 24,
                    "parent_id" => 9,
                    "permission" => NULL,
                    "title" => "Routes",
                    "uri" => "helpers/routes"
                ],
                [
                    "icon" => "fa-database",
                    "order" => 25,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "Log viewer",
                    "uri" => "logs"
                ],
                [
                    "icon" => "fa-user-secret",
                    "order" => 12,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "管理者",
                    "uri" => "admin-users"
                ],
                [
                    "icon" => "fa-bar-chart",
                    "order" => 13,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "Dashboard",
                    "uri" => "dashboard"
                ],
                [
                    "icon" => "fa-info-circle",
                    "order" => 10,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "お知らせ",
                    "uri" => "notices"
                ],
                [
                    "icon" => "fa-taxi",
                    "order" => 4,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "タクシー・バス会社",
                    "uri" => "clients-taxibus"
                ],
                [
                    "icon" => "fa-list-alt",
                    "order" => 9,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "議事録",
                    "uri" => "meetings"
                ],
                [
                    "icon" => "fa-file-o",
                    "order" => 8,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "日報",
                    "uri" => "reports"
                ],
                [
                    "icon" => "fa-truck",
                    "order" => 5,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "トラック会社",
                    "uri" => "clients-truck"
                ],
                [
                    "icon" => "fa-beer",
                    "order" => 6,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "飲食店",
                    "uri" => "clients-restaurant"
                ],
                [
                    "icon" => "fa-plane",
                    "order" => 7,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "旅行業者など",
                    "uri" => "clients-travel"
                ],
                [
                    "icon" => "fa-list",
                    "order" => 3,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "社内ToDoリスト",
                    "uri" => "office-todos"
                ],
                [
                    "icon" => "fa-list",
                    "order" => 2,
                    "parent_id" => 0,
                    "permission" => NULL,
                    "title" => "営業ToDoリスト",
                    "uri" => "sales-todos"
                ]
            ]
        );

        \Encore\Admin\Auth\Database\Permission::truncate();
        \Encore\Admin\Auth\Database\Permission::insert(
            [
                [
                    "http_method" => "",
                    "http_path" => "*",
                    "name" => "All permission",
                    "slug" => "*"
                ],
                [
                    "http_method" => "GET",
                    "http_path" => "/",
                    "name" => "Dashboard",
                    "slug" => "dashboard"
                ],
                [
                    "http_method" => "",
                    "http_path" => "/auth/login\r\n/auth/logout",
                    "name" => "Login",
                    "slug" => "auth.login"
                ],
                [
                    "http_method" => "GET,PUT",
                    "http_path" => "/auth/setting",
                    "name" => "User setting",
                    "slug" => "auth.setting"
                ],
                [
                    "http_method" => "",
                    "http_path" => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
                    "name" => "Auth management",
                    "slug" => "auth.management"
                ],
                [
                    "http_method" => "",
                    "http_path" => "/helpers/*",
                    "name" => "Admin helpers",
                    "slug" => "ext.helpers"
                ],
                [
                    "http_method" => "",
                    "http_path" => "/logs*",
                    "name" => "Logs",
                    "slug" => "ext.log-viewer"
                ],
                [
                    "http_method" => "",
                    "http_path" => "/\r\n/users*\r\n/admin-users*\r\n/sales-todos*\r\n/office-todos*\r\n/clients-taxibus*\r\n/clients-truck*\r\n/clients-restaurant*\r\n/clients-travel*\r\n/business-districts*\r\n/vehicles-taxi*\r\n/vehicles-bus*\r\n/branches*\r\n/contact-persons*\r\n/reports*\r\n/report-contents-sales*\r\n/report-contents-work*\r\n/report-comments*\r\n/meetings*\r\n/notices*",
                    "name" => "管理ユーザー利用可能機能",
                    "slug" => "adminuser"
                ]
            ]
        );

        \Encore\Admin\Auth\Database\Role::truncate();
        \Encore\Admin\Auth\Database\Role::insert(
            [
                [
                    "name" => "Administrator",
                    "slug" => "administrator"
                ],
                [
                    "name" => "管理ユーザー",
                    "slug" => "adminuser"
                ]
            ]
        );

        // pivot tables
        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "menu_id" => 2,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 3,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 4,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 5,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 6,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 7,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 9,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 10,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 11,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 12,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 14,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 16,
                    "role_id" => 1
                ],
                [
                    "menu_id" => 15,
                    "role_id" => 2
                ]
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [
                [
                    "permission_id" => 1,
                    "role_id" => 1
                ],
                [
                    "permission_id" => 8,
                    "role_id" => 2
                ]
            ]
        );

        // finish
    }
}
