<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

    /**
     * {{DOMAIN}}/user/auth/register
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['email', 'name']);

        $isRegistered = $this->userRepo->all(['email' => $input['email']]);

        if (count($isRegistered)) {
            return $this->response(422, [], __('Email already exits'));
        }

        $password = $request->request->get('password');
        $input['password'] = bcrypt($password);

        $user = $this->userRepo->create($input);

        return $this->response(200, ['user' => new UserResource($this->userRepo->find($user->id))], __('text.register_successfully'));
    }
}
