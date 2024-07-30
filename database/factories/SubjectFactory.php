<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'nama' => $this->faker->word,
            'type' => $this->faker->randomElement(['Non-Teknis', 'Teknis']),
        ];
    }
}
