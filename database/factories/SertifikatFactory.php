<?php

namespace Database\Factories;

use App\Models\dudi;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SertifikatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid()->toString(),
            'siswa_id' => Siswa::factory(),
            'dudi_id' => dudi::factory(),
            'kompetensi_keahlian' => $this->faker->jobTitle,
            'alamat_tempat_pkl' => $this->faker->address,
            'tanggal_mulai' => $this->faker->date(),
            'tanggal_selesai' => $this->faker->date(),
            'nilai_id' => Nilai::factory(),
            'predikat' => $this->faker->randomElement(['A', 'B', 'C']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
