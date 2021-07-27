<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        $classExp = get_class($exception);

        if($classExp == 'Illuminate\Auth\AuthenticationException'){
            return response()->json([
                'code' => 401,
                'data' => (object)[],
                'message' => '1Please log in to do this',//Please login to perform this action
                'errors' => ''
            ], 401);
        }

        return parent::render($request, $exception);
    }
}
