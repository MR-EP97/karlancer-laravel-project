<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{

    public function success(string $message = '', array $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status' => $code
        ]);
    }

    public function error(string $message = '', int $code = 404): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $code
        ]);
    }

}
