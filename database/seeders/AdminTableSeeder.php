<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->delete();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('Passw0rd!'),
            'role' => 'admin'
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => "api"]);
        $userRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => "api"]);
        $managerRole = Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => "api"]);

        $admin->assignRole($adminRole);
        $permissions = [

            'create teams',
            'update teams',
            'delete teams',

            'create projects',
            'update projects',
            'delete projects',


            'create comments',
            'update owner comments',
            'delete comments',


        ];

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }


        $userPermissions = [
            "create teams",
            'update owner teams',
            'delete owner teams',
            'view owner teams',

            'create projects', 
            'update owner projects', 
            'delete owner projects',

            'create tasks'
            , 'update own tasks'
            , 'delete own tasks',

            'create comments',
            'update owner comments',
            'delete comments',

        ];

        foreach ($userPermissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $userRole->givePermissionTo($perm);
        }


        $managerPermissions = [
            // Projects
           'create projects',
            'update projects',
            'delete projects',

            // Tasks
            'create tasks',
            'view tasks',
            'update tasks',
            'delete tasks',
            

            'create comments',
            'update comments',
            'delete comments',

           
        ];


        foreach ($managerPermissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $managerRole->givePermissionTo($perm);
        }
    }
}
