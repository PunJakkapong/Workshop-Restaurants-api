<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        /**
         * Search for restaurants using Google Places API
         * 
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * 
         * Flow:
         * 1. Get keyword and location from request
         * 2. If keyword is 'Bang sue', use hardcoded coordinates
         * 3. If location provided, geocode it to get coordinates
         * 4. Search for restaurants using Places API
         * 5. Return results as JSON
         */

        // get keyword from request set default = 'Bang sue', this key is required
        $keyword = trim($request->query('keyword', 'Bang sue'));

        // get address from request set default = '', this key is not required
        $address = trim($request->query('address', ''));

        // set location as default location, address is 'Bang sue'
        $location = '13.8063886, 100.5307932';

        // if location provided, get location from google API geocode
        if (!empty($address)) {
            $cacheKey = 'geocode_' . md5($address);

            // cache for 24 hours to reduce API calls and improve performance
            $location = cache()->remember($cacheKey, now()->addHours(24), function () use ($address, $location) {
                $geocodeClient = new \GuzzleHttp\Client();
                $geocodeResponse = $geocodeClient->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'query' => [
                        'address' => $address,
                        'key' => env('GOOGLE_MAPS_API_KEY')
                    ]
                ]);

                $geocodeData = json_decode($geocodeResponse->getBody(), true);
                // if found return data from google API geocode
                if (isset($geocodeData['results'][0]['geometry']['location'])) {
                    return $geocodeData['results'][0]['geometry']['location']['lat'] . ',' .
                        $geocodeData['results'][0]['geometry']['location']['lng'];
                }

                // if not found return default location
                return $location;
            });
        }


        // search for restaurants using google places API
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query' => [
                'location' => $location,
                'radius' => 3000,
                'type' => 'restaurant',
                'query' => $keyword,
                'key' => env('GOOGLE_MAPS_API_KEY')
            ]
        ]);

        // return results as json
        $places = json_decode($response->getBody(), true);
        return response()->json($places);
    }
}