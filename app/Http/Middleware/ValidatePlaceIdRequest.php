<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidatePlaceIdRequest
{
  public function handle(Request $request, Closure $next)
  {
    $placeId = $request->route('place_id');

    if (empty($placeId)) {
      return response()->json([
        'error' => 'Missing required parameter',
        'message' => 'The place_id parameter is required'
      ], 400);
    }

    return $next($request);
  }
}