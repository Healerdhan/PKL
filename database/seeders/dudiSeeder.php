<?php

namespace Database\Seeders;

use App\Models\dudi;
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
        dudi::factory()->count(10)->create();
    }
}
