<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;

class EnsureLoginIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $extendRoute = [
            'logout',
            'forgot',
            'reset',
            'login'
        ];
        if (!in_array($request->route()->getName(), $extendRoute)) {
            $validLogin = auth()->user();

            if ($validLogin && $validLogin->id) {
                $admin = User::find($validLogin->id);

                $token = auth()->tokenById($validLogin->id) ? auth()->tokenById($validLogin->id) : 'tmp-token';
                $input = [
                    'token' => substr($token, 0, 100),
                    'ip' => $request->getClientIp(),
                    'updated_at' => \Carbon\Carbon::now()
                ];
                $admin->update($input);
            }
        }
        return $next($request);
    }
}
