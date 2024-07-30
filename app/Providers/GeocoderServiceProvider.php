<?php

namespace App\Providers;

use Geocoder\Provider\GoogleMaps\GoogleMaps;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;
use Geocoder\StatefulGeocoder;

class GeocoderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('geocoder', function ($app) {
            $httpClient = new GuzzleClient();
            $provider = new GoogleMaps($httpClient, null, env('GOOGLE_MAPS_GEOCODING_API_KEY'));
            return new StatefulGeocoder($provider, 'en');
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
