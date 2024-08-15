<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus cache peran dan izin
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat peran Super Admin
        $superAdminRole = Role::create(['name' => 'super-admin']);

        // Buat peran Admin
        $adminRole = Role::create(['name' => 'admin']);

        // Buat peran User
        $userRole = Role::create(['name' => 'user']);

        // Buat izin dasar yang bisa diberikan (opsional, sesuaikan dengan kebutuhan Anda)
        $permissions = [
            'view dashboard',

            'view categories',
            'create category',
            'update category',
            'delete category',
            'delete multiple categories',

            // Siswa permissions
            'view siswa',
            'create siswa',
            'update siswa',
            'delete siswa',
            'delete multiple siswa',

            // Dudi permissions
            'view dudi',
            'create dudi',
            'update dudi',
            'delete dudi',
            'delete multiple dudi',

            // Pembimbing permissions
            'view pembimbing',
            'create pembimbing',
            'update pembimbing',
            'delete pembimbing',
            'delete multiple pembimbing',

            // Subject permissions
            'view subjects',
            'create subject',
            'update subject',
            'delete subject',
            'delete multiple subjects',

            // Nilai permissions
            'view nilai',
            'create nilai',
            'update nilai',
            'delete nilai',
            'delete multiple nilai',

            // Sertifikat permissions
            'view sertifikat',
            'create sertifikat',
            'update sertifikat',
            'delete sertifikat',
            'delete multiple sertifikat',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Berikan semua izin ke Super Admin (opsional)
        $superAdminRole->syncPermissions(Permission::all());

        // Berikan izin terbatas ke Admin
        $adminPermissions = collect($permissions)->filter(function ($permission) {
            return str_contains($permission, 'create') || str_contains($permission, 'delete') || str_contains($permission, 'view');
        });
        $adminRole->givePermissionTo($adminPermissions);

        // Berikan izin hanya untuk melihat ke User
        $userPermissions = collect($permissions)->filter(function ($permission) {
            return str_contains($permission, 'view');
        });
        $userRole->givePermissionTo($userPermissions);

        // Assign role ke user tertentu
        $superAdmin = User::find(1);
        if ($superAdmin) {
            $superAdmin->assignRole($superAdminRole);
        }

        $admin = User::find(2);
        if ($admin) {
            $admin->assignRole($adminRole);
        }

        $user = User::find(3);
        if ($user) {
            $user->assignRole($userRole);
        }
    }
}
