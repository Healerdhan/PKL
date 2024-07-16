<?php

namespace Database\Factories;

use App\Models\dudi;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class dudiFactory extends Factory
{

    protected $model = dudi::class;

    public function definition()
    {

        return [
            'id' => Str::uuid(),
            'tempat' => $this->faker->city,
            'jumlah' => $this->faker->numberBetween(1, 14),
            'siswa_id1' => Siswa::factory(),
            'siswa_id2' => Siswa::factory(),
            'siswa_id3' => Siswa::factory(),
            'siswa_id4' => Siswa::factory(),
            'siswa_id5' => Siswa::factory(),
            'siswa_id6' => Siswa::factory(),
            'siswa_id7' => Siswa::factory(),
            'siswa_id8' => Siswa::factory(),
            'siswa_id9' => Siswa::factory(),
            'siswa_id10' => Siswa::factory(),
            'siswa_id11' => Siswa::factory(),
            'siswa_id12' => Siswa::factory(),
            'siswa_id13' => Siswa::factory(),
            'siswa_id14' => Siswa::factory(),
        ];
    }
}
