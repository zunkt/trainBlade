<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function response($code = 200, $data = [], $message = '', $errors = [], $extendCode = null, $status = '')
    {
        return response()->json([
            'code' => $extendCode ? $extendCode : $code,
            'data' => (object)$data,
            'message' => $errors ? array_values($errors->toArray())[0][0] : $message,
            'errors' => (object)$errors,
            'status' => ($status == false) ? false : true
        ], $code);
    }

}
