<?php

namespace App\Http\Responses;

use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $code);
    }

    public static function error($message = 'Error', $errors = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $code);
    }

    /**
     * A page of a list: data => { items: [...], meta: { current_page, last_page, per_page, total } }
     * $resource is the Resource class used for every item.
     */
    public static function paginated(LengthAwarePaginator $paginator, string $resource, $message = 'Success')
    {
        return self::success([
            'items' => $resource::collection($paginator->getCollection()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], $message);
    }
}
