<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        $this->middleware('auth:user');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $pages = intval($request->size);
        $users = $this->userRepo->userSearch($request)->paginate($pages);
        return $this->response(200, ['users' => new UserCollection($users)], __('text.retrieved_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->userRepo->find($id);

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        return $this->response(200, ['user' => new UserResource($user)], __('text.retrieved_successfully'));
    }
}
