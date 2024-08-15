<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
        ])->assignRole('super-admin');

        $this->call([
            RolesAndPermissionsSeeder::class,
            CategorySeeder::class,
            SiswaSeeder::class,
            dudiSeeder::class,
            PembimbingSeeder::class,
            SubjectSeeder::class,
            NilaiSeeder::class,
            SertifikatSeeder::class
        ]);
    }
}
