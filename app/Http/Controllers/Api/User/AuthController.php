<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->guard = 'user';

        $this->middleware('auth:' . $this->guard, ['except' => ['login', 'sendPasswordResetEmail', 'resetPassword', 'me', 'register']]);

        $this->userRepo = $userRepo;
    }

    /**
     * {{DOMAIN}}/user/auth/login
     *
     * @return JsonResponse
     */
    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => [
                'required',
            ]
        ]);
        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $credentials = request(['email', 'password']);

        if (!$token = auth('user')->attempt($credentials)) {
            // Validate rule: Incorrect Password type cap :
            $email = trim(request()->email);
            $userFailed = User::where('email', $email)->first();

            if (isset($userFailed)) {
                return $this->response(422, [], __('Password Wrong'));
            }
            return $this->response(401, [], __('auth.failed'));
        }
        $user = User::find(auth('user')->id());

        // Reset failed_login_attempts
        $user->update(['failed_login_attempts' => 0]);

        return $this->response(200, [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('user')->factory()->getTTL() * 60
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            User::find(auth('user')->id())->update(['token' => null]);
            auth('user')->logout();
            return $this->response(200, [], __('Logout Successful'));
        } catch (Throwable $e) {
            return $this->response(422, [], __('Please Login!'));
        }

    }
}
