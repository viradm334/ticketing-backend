<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Context;
use Traversable;

class ApiResponse
{
    public static function json(
        $data = null,
        $message = 'Request successful',
        $statusCode = 200,
        $status = 'success',
        $isCollectionResource = false,
        $meta = []
    ): JsonResponse {
        $resp = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $resp['meta'] = $meta;
        }

        if ($isCollectionResource) {
            // avoid wrapping data under data key once again.
            $resp = array_merge($resp, $data->toArray(request()));
        }

        // add pagination meta
        if (isset($data->resource) && ($data->resource instanceof AbstractPaginator)) {
            $paginatedData = $data->resource->toArray();
            unset($paginatedData['data']);
            $resp = array_merge($resp, ['meta' => $paginatedData]);
        }

        if ($data instanceof AbstractPaginator) {
            $paginatedData = $data->toArray();
            $resp['data'] = $paginatedData['data'];
            unset($paginatedData['data']);
            $resp = array_merge($resp, ['meta' => $paginatedData]);
        }

        return response()->json($resp, $statusCode);
    }

    public static function forbidden($message = 'Forbidden', $statusCode = 403): JsonResponse
    {
        return self::json(null, $message, $statusCode, 'error');
    }

    public static function error($message = 'An error occurred', $statusCode = 400): JsonResponse
    {
        $meta = [];
        if($statusCode == 500) {
            $meta = [
                'trace_id' => Context::get('trace_id')
            ];
        }

        return self::json(null, $message, $statusCode, 'error', false, $meta);
    }

    public static function success($message, $data = null, $statusCode = 200): JsonResponse
    {
        return self::json($data, $message, $statusCode);
    }

    public static function validationError($errors, $message = 'Validation failed'): JsonResponse
    {
        return self::json($errors, $message, 422, 'validation_error');
    }

    public static function resource($resource, $message = 'Request successful', $statusCode = 200): JsonResponse
    {
        return self::json($resource, $message, $statusCode);
    }

    public static function collection($collection, $message = 'Items retrieved successfully', $statusCode = 200, $meta = []): JsonResponse
    {
        $message = $collection->isEmpty() ? "No items retrieved" : $message;

        return self::json($collection, $message, $statusCode, meta: $meta);
    }

    // custom data structure, needed for nested resources
    public static function customCollection(array $collection, $meta = [], $message = 'Items retrieved successfully', $statusCode = 200): JsonResponse
    {
        $message = self::isCollectionEmpty($collection) ? "No items retrieved" : $message;

        $json = [
            'status' => 'success',
            'message' => $message,
            'data' => $collection,
        ];

        if (!empty($meta)) {
            $json['meta'] = $meta;
        }

        return response()->json($json, $statusCode);
    }

    private static function isCollectionEmpty($data): bool
    {
        if (empty($data)) {
            return true;
        }

        if (is_array($data) || $data instanceof Traversable) {
            foreach ($data as $value) {
                if (!self::isCollectionEmpty($value)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public static function collectionResource($collection, $message = 'Items retrieved successfully', $statusCode = 200): JsonResponse
    {
        $message = $collection->isEmpty() ? "No items retrieved" : $message;

        return self::json($collection, $message, $statusCode, 'success', true);
    }
}
