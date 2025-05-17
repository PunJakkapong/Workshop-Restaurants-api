<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidatePhotoRequest
{
  public function handle(Request $request, Closure $next)
  {
    $allowedParams = ['photo_reference'];
    $requestParams = array_keys($request->all());

    // check if any parameters other than allowed
    $invalidParams = array_diff($requestParams, $allowedParams);

    if (!empty($invalidParams)) {
      return response()->json([
        'error' => 'Invalid parameters',
        'message' => 'Only photo_reference parameter is allowed',
        'invalid_params' => array_values($invalidParams)
      ], 400);
    }

    // check if photo_reference is exist
    if (!$request->has('photo_reference')) {
      return response()->json([
        'error' => 'Missing required parameter',
        'message' => 'The photo_reference parameter is required'
      ], 400);
    }

    return $next($request);
  }
}