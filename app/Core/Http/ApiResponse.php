<?php

namespace App\Core\Http;

use Illuminate\Http\JsonResponse;

/**
 * One response envelope for every API / AJAX endpoint so the frontend
 * never has to special-case shapes.
 */
class ApiResponse
{
    public static function success(mixed $data = null, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json(array_filter([
            'success' => true,
            'data'    => $data,
            'meta'    => $meta ?: null,
        ], fn ($value) => $value !== null), $status);
    }

    public static function error(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
