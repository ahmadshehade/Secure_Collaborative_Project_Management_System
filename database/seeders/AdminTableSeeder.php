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
        Role::firstOrCreate(['name' => 'member', 'guard_name' => "api"]);
        Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => "api"]);

        $admin->assignRole($adminRole);
    
    }
}
