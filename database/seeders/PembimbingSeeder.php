<?php

namespace Database\Seeders;

use App\Models\dudi;
use App\Models\Pembimbing;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Seeder;

class PembimbingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pembimbing::factory()->count(15)->create()->each(function ($pembimbing) {
            $dudis = dudi::factory(3)->create();
            $pembimbing->dudis()->attach($dudis->pluck('id')->toArray());
        });
    }
}
