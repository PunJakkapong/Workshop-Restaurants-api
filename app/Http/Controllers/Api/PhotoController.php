<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class PhotoController extends Controller
{
  public function index()
  {
    // google photo reference from places API
    $photoReference = request()->query('photo_reference');

    // default width of the photo 400px
    $maxWidth = request()->query('maxwidth', 400);

    if (!$photoReference) {
      return response()->json(['error' => 'Photo reference is required'], 400);
    }

    // generate unique cache key for photo
    $cacheKey = "photo_{$photoReference}_{$maxWidth}";

    // cache for 24 hours to reduce API calls and improve performance
    return cache()->remember($cacheKey, now()->addHours(24), function () use ($photoReference, $maxWidth) {
      $client = new \GuzzleHttp\Client();
      $response = $client->get('https://maps.googleapis.com/maps/api/place/photo', [
        'query' => [
          'maxwidth' => $maxWidth,
          'photo_reference' => $photoReference,
          'key' => env('GOOGLE_MAPS_API_KEY')
        ]
      ]);

      return response($response->getBody())
        ->header('Content-Type', $response->getHeader('Content-Type')[0]);
    });
  }

}