<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $pages = intval($request->size);
        $users = $this->userRepo->userSearch($request)->paginate($pages);
        return view('user.index', ['users' => $users, 'pages' => $pages]);
    }

    public function create()
    {
        return view('user.create');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepo->find($id);

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        return view('user.show', ['user' => $user]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function store(Request $request)
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

        $this->userRepo->create($input);

        return redirect()->route('user.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->response(422, [], '', $validator->errors());
        }

        $input = $request->only(['email', 'name']);

        $password = $request->request->get('password');
        $input['password'] = bcrypt($password);

        $this->userRepo->update($input, $id);
        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        /** @var User $user */
        $user = $this->userRepo->find($request->id);

        if (empty($user)) {
            return $this->response(422, [], __('text.this_user_is_invalid'));
        }

        $this->userRepo->delete($request->id);

        return $this->response(200, null, 'User deleted successfully', [], null, true);
    }
}
