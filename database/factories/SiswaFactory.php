<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition()
    {
        return [
            'id' => (string) Str::orderedUuid(),
            'nama_siswa' => $this->faker->name,
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'NISN' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $this->faker->date,
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
