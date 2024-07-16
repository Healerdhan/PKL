<?php

namespace Database\Factories;

use App\Models\dudi;
use App\Models\Pembimbing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PembimbingFactory extends Factory
{
    protected $model = Pembimbing::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'nama_pegawai' => $this->faker->name(),
            'dudi_id1' => dudi::factory(),
            'dudi_id2' => dudi::factory(),
            'dudi_id3' => dudi::factory(),
            'dudi_id4' => dudi::factory(),
            'dudi_id5' => dudi::factory(),
        ];
    }
}
