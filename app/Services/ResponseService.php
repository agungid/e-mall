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
    public static function toJson($status,$message, $code = 500, $data = null) {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}
