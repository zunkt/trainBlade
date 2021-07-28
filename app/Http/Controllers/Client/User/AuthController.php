<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * @param Request $request
     */
    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            $email = trim(request()->email);
            $userFailed = User::where('email', $email)->first();
            if (isset($userFailed)) {
                return redirect()->back()->withErrors([
                    'password' => 'Wrong password. Please Check Again!',
                ]);
            }
        }

        return redirect()->intended('list');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            Auth::logout();
            return redirect()->route('main');
        } catch (Throwable $e) {
            return redirect()->back()->withErrors([
                'login' => 'Please Login!'
            ]);
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
            'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['email', 'name', 'password']);

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
