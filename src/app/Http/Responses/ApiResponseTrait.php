<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
  protected function success(
    string $message = 'OK',
    $data = null,
    ?array $meta = null,
    int $status = 200
  ): JsonResponse {
    $response = [
      'success' => true,
      'code' => $status,
      'message' => $message,
      'data' => $data,
    ];

    if ($meta !== null) {
      $response['meta'] = $meta;
    }

    return response()->json($response, $status);
  }

  protected function error(
    string $message = 'Bad Request',
    int $status = 400,
    ?array $errors = null
  ): JsonResponse {
    return response()->json([
      'success' => false,
      'code' => $status,
      'message' => $message,
      'errors' => $errors,
    ], $status);
  }
}
