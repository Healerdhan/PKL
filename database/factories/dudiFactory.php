<?php

namespace Database\Factories;

use App\Models\Dudi;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DudiFactory extends Factory
{
    protected $model = Dudi::class;

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

            Log::info('Geocoder response for Dudi', ['address' => $address, 'coordinates' => $coordinates]);
        } catch (\Exception $e) {
            Log::error('Geocoder request failed for Dudi', ['address' => $address, 'error' => $e->getMessage()]);
        }

        return [
            'id' => (string) Str::orderedUuid(),
            'tempat' => $this->faker->company,
            'jumlah' => $this->faker->numberBetween(1, 100),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
