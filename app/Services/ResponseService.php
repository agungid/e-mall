<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;

class ResponseService 
{    
    /**
     * return json response data
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $data
     * @param  mixed $code
     * @return void
     */
    public static function toJson($status,$message, $code = 500, $data = [], $error = []) {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
            'error'    => $error
        ], $code);
    }

    public static function toArray($status, $message, $resources = []) 
    {
        return [
            'status'       => $status,
            'message'      => $message,
            'data'         => $resources,
        ];
    }
}
