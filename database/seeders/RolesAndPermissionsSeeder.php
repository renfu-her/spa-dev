<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 重置角色和權限的快取
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 定義權限和模組的對應關係
        $permissions = [
            // 供應商模組
            'view_suppliers' => ['module' => '供應商管理', 'description' => '查看供應商'],
            'create_suppliers' => ['module' => '供應商管理', 'description' => '新增供應商'],
            'edit_suppliers' => ['module' => '供應商管理', 'description' => '編輯供應商'],
            'delete_suppliers' => ['module' => '供應商管理', 'description' => '刪除供應商'],
            
            // 採購單模組
            'view_purchase_orders' => ['module' => '採購管理', 'description' => '查看採購單'],
            'create_purchase_orders' => ['module' => '採購管理', 'description' => '新增採購單'],
            'edit_purchase_orders' => ['module' => '採購管理', 'description' => '編輯採購單'],
            'delete_purchase_orders' => ['module' => '採購管理', 'description' => '刪除採購單'],
            
            // 使用者管理模組
            'view_users' => ['module' => '使用者管理', 'description' => '查看使用者'],
            'create_users' => ['module' => '使用者管理', 'description' => '新增使用者'],
            'edit_users' => ['module' => '使用者管理', 'description' => '編輯使用者'],
            'delete_users' => ['module' => '使用者管理', 'description' => '刪除使用者'],
            
            // 角色權限管理模組
            'view_roles' => ['module' => '角色權限管理', 'description' => '查看角色'],
            'create_roles' => ['module' => '角色權限管理', 'description' => '新增角色'],
            'edit_roles' => ['module' => '角色權限管理', 'description' => '編輯角色'],
            'delete_roles' => ['module' => '角色權限管理', 'description' => '刪除角色'],
        ];

        // 建立權限
        foreach ($permissions as $name => $details) {
            Permission::create([
                'name' => $name,
                'module' => $details['module'],
                'description' => $details['description'],
                'guard_name' => 'web',
            ]);
        }

        // 建立角色並賦予權限
        // 管理者
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // 經理
        $manager = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo(Permission::all());

        // 員工
        $staff = Role::create(['name' => 'staff', 'guard_name' => 'web']);
        $staff->givePermissionTo([
            'view_suppliers',
            'view_purchase_orders',
            'view_users',
        ]);

        // 操作者
        $operator = Role::create(['name' => 'operator', 'guard_name' => 'web']);
        $operator->givePermissionTo([
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
        ]);
    }
} 