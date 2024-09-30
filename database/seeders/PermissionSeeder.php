<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["super-admin", "admin", "locality-admin", "staff", "seller", "manager", "buyer", "field-staff"];
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            "super-admin" => [
                "admin" => ['c', 'r', 'u', 'd'],
                "settings" => ['c', 'r', 'u', 'd'],
                "user" => ['c', 'r', 'u', 'd'],
                "role" => ['c', 'r', 'u', 'd'],
                "permission" => ['c', 'r', 'u', 'd'],
            ],
            "admin" => [
                "user" => ['c', 'r', 'u', 'd'],
            ],
        ];

        // assign permision to superadmin role
        foreach ($permissions["super-admin"] as $perm => $ops) {
            foreach ($ops as $op) {
                $permission = Permission::firstOrCreate(['name' => $this->getOperation($op, $perm)]);
            }
        }
        Role::where('name', 'super-admin')->first()->givePermissionTo(Permission::all());

        // for admin
        foreach ($permissions["admin"] as $perm => $ops) {
            foreach ($ops as $op) {
                $permission = Permission::firstOrCreate(['name' => $this->getOperation($op, $perm)]);
                $permission->assignRole("admin"); //assign role of admin o each permission
            }
        }

        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    function getOperation(string $val, string $perm): string
    {
        $ops = ['c' => 'create', "r" => 'read', "u" => 'update', "d" => 'delete'];
        return $ops[$val] . " " . $perm;
    }
}