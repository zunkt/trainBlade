<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use PHPlot;

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
        $size = 4;
        $pages = intval($size);
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
            'password' => 'required|string|max:255',
            'height' => 'required|double|max:100',
            'weight' => 'required|double|max:250'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $input = $request->only(['email', 'name', 'password', 'height', 'weight']);

        $isRegistered = $this->userRepo->all(['email' => $input['email']]);

        if (count($isRegistered)) {
            return redirect()->back()->withErrors([
                'isExitEmail' => "Email Exits!"
            ]);
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
            'height' => 'required|double|max:100',
            'weight' => 'required|double|max:250'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $input = $request->only(['email', 'name', 'height', 'weight']);

        if ($request->request->get('password')) {
            $password = $request->request->get('password');
            $input['password'] = bcrypt($password);
        }

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

    public function viewchart()
    {
        return view('phplot.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function phplot(Request $request)
    {
        $users = $this->userRepo->userSearch($request)->get();
        $maxHeight = $users->filter()->max('height');
        $maxWeight = $users->filter()->max('weight');
        $data = array();

        foreach ($users as $user) {
            $data[] = array('', (int)$user->height, (int)$user->weight);
        }

        $plot = new PHPlot(800, 600);
        $plot->SetImageBorderType('plain');

        $plot->SetPlotType('points');
        $plot->SetDataType('data-data');

        # Main plot title:
        $plot->SetTitle('Scatterplot (User plot)');

        $plot->SetXTitle('weight');
        $plot->SetYTitle('height');
        $plot->SetDataValues($data);

        # Need to set area and ticks to get reasonable choices.
        $plot->SetPlotAreaWorld(0, 0, $maxWeight, $maxHeight);
        $plot->SetXTickIncrement(5);
        $plot->SetYTickIncrement(5);

        $plot->SetYTickPos('yaxis');


        # Turn on 4 sided borders, now that axes are inside:
        $plot->SetPlotBorderType('full');

        # Draw both grids:
        $plot->SetDrawXGrid(True);
        $plot->SetDrawYGrid(True);  # Is default

//        $plot->SetIsInline(true);
//        $plot->SetOutputFile("test.png");

        $plot->DrawGraph();

        return view('phplot.index');
    }
}
