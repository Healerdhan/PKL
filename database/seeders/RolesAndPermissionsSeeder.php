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
            'edit articles',
            'delete articles',
            'publish articles',
            'unpublish articles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Berikan semua izin ke Super Admin (opsional)
        $superAdminRole->syncPermissions(Permission::all());

        // Berikan izin terbatas ke Admin (opsional, sesuaikan dengan kebutuhan Anda)
        $adminRole->givePermissionTo(['edit articles', 'publish articles']);

        // Assign role ke user tertentu
        $superAdmin = User::find(1); // Misalnya user dengan ID 1 adalah Super Admin
        if ($superAdmin) {
            $superAdmin->assignRole($superAdminRole);
        }

        $admin = User::find(2); // Misalnya user dengan ID 2 adalah Admin
        if ($admin) {
            $admin->assignRole($adminRole);
        }

        $user = User::find(3); // Misalnya user dengan ID 3 adalah User biasa
        if ($user) {
            $user->assignRole($userRole);
        }
    }
}
