<?php

namespace Database\Seeders;

use App\Models\dudi;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class dudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // dudi::factory()->count(10)->create();

        Dudi::factory(10)->create()->each(function ($dudi) {
            $siswaIds = Siswa::inRandomOrder()->take(5)->pluck('id')->toArray();
            $dudi->siswas()->attach($siswaIds);
        });
    }
}
