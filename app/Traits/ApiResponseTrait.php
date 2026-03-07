<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;


trait ApiResponseTrait
{
     protected function successResponse(
        string $message = 'Success',
        $data = null,
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $statusCode,
            'data'    => $data
        ], $statusCode);
    }

    protected function errorResponse(
        string $message = 'Something went wrong',
        int $statusCode = 400,
        $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
            'errors'  => $errors
        ], $statusCode);
    }

    protected function validationErrorResponse(
        string $message = 'Validation error occured',
        int $statusCode = 422,
        $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
            'errors'  => $errors
        ], $statusCode);
    }

    protected function authResponse(
        string $message,
        $user,
        string $token,
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $statusCode,
            'user'    => $user,
            'token'   => $token
        ], $statusCode);
    }

    protected function paymentError(
        string $message,
        // $user,
        // string $token,
        int $statusCode = 405
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }

    protected function authenticationError(
        string $message = 'User is not Authenticated',
        int $statusCode = 401
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }
    protected function authortizationError(
        string $message = 'User is not Authortization',
        int $statusCode = 401
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }
}
