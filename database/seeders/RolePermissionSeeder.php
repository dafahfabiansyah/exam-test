<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class RolePermissionSeeder extends Seeder
{
    use HasRoles;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // create permission 
        $permission = [
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
        ];
        foreach ($permission as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }
        // teacher role and permission
        $teacherRole = Role::create(['name' => 'teacher']);
        $teacherRole ->givePermissionTo([
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
        ]);
        // student role and permission
        $studentRole = Role::create(['name' => 'student']);
        $studentRole ->givePermissionTo([
            'view courses',
        ]);

        // create user "super admin"
        $user = User::create([
            'name' => 'admin dapa',
            'email' => 'dapa@teacher.com',
            "password" => bcrypt('12345678'),
            // "email_verified_at" => now()
        ]);
        $user->assignRole($teacherRole);
    }
}
