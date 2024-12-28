<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as Base;

class Controller extends Base
{
    use AuthorizesRequests, ValidatesRequests;
    protected function success($data = [], $message = "Success", $code = 200)
    {
        $response = [];
        if(!empty($data)){
            $response += [
                'data' => $data
            ];
        }
        $response += [
            'message' => $message,
        ];
        return response()->json($response, $code);
    }

    protected function error($message, $code = 400)
    {
        $response = [
            'message' => $message,
            'errors' => [$message],
        ];
        return response()->json($response, $code);
    }

}
