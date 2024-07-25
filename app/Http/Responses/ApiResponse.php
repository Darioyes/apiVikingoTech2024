<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function successAuth($message = '', $code = Response::HTTP_OK, $token='', $data = [])
    {
        return response()->json([
            'response' => 'success',
            'message' => $message,
            'token' => $token,
            'data' => $data,
            'error' => false
        ], $code);
    }

    public static function success($message = '', $code = Response::HTTP_OK, $data = [])
    {
        return response()->json([
            'response' => 'success',
            'message' => $message,
            'data' => $data,
            'error' => false
        ], $code);
    }

    public static function error($message = '', $code = Response::HTTP_BAD_REQUEST, $data = [])
    {
        return response()->json([
            'response' => 'error',
            'message' => $message,
            'data' => $data,
            'error' => true
        ], $code);
    }

}
