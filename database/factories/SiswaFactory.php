<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition()
    {
        $address = $this->faker->address;
        $client = new Client(['verify' => false]);

        $latitude = null;
        $longitude = null;

        try {
            $response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'address' => $address,
                    'key' => env('GEOCODER_GOOGLE_MAPS_API_KEY'),
                ],
            ]);

            $coordinates = json_decode($response->getBody()->getContents(), true);

            if (!empty($coordinates['results'])) {
                $latitude = $coordinates['results'][0]['geometry']['location']['lat'];
                $longitude = $coordinates['results'][0]['geometry']['location']['lng'];
            }

            Log::info('Geocoder response for Siswa', ['address' => $address, 'coordinates' => $coordinates]);
        } catch (\Exception $e) {
            Log::error('Geocoder request failed for Siswa', ['address' => $address, 'error' => $e->getMessage()]);
        }

        return [
            'id' => (string) Str::orderedUuid(),
            'nama_siswa' => $this->faker->name,
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'NISN' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'alamat' => $address,
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $this->faker->date,
            'category_id' => Category::inRandomOrder()->first()->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
}
