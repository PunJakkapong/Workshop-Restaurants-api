<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateRestaurantRequest
{
  public function handle(Request $request, Closure $next)
  {
    $allowedParams = ['keyword', 'address', 'next_page_token'];
    $requestParams = array_keys($request->all());

    // check if any parameters other than allowed
    $invalidParams = array_diff($requestParams, $allowedParams);

    if (!empty($invalidParams)) {
      return response()->json([
        'error' => 'Invalid parameters',
        'message' => 'Only keyword, address and next_page_token parameters are allowed',
        'invalid_params' => array_values($invalidParams)
      ], 400);
    }

    // check if keyword is exist
    if (!$request->has('keyword')) {
      return response()->json([
        'error' => 'Missing required parameter',
        'message' => 'The keyword parameter is required'
      ], 400);
    }

    return $next($request);
  }
}