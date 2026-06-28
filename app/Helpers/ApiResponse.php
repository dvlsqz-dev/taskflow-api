<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
  
    /**
     * Respuesta exitosa estandarizada.
     *
     * @param  mixed   $data
     * @param  string  $message
     * @param  int     $code
     * @return JsonResponse
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Respuesta de error estandarizada.
     *
     * @param  string  $message
     * @param  mixed   $errors
     * @param  int     $code
     * @return JsonResponse
     */
    public static function error(
        string $message = 'Error',
        mixed $errors = null,
        int $code = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

}