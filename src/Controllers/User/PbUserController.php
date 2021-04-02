<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\User;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbUserController extends Controller
{
    protected $aeas;

    function __construct()
    {
        $this->aeas = new AeasHelpers();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $users = PbUser::latest()->paginate(5);
        $filtered = $users->map(function ($user) {
            return $user->only(['id', 'name', 'email', 'last_session', 'created_at']);
        })->sortByDesc(['name', 'email']);

        $filtered = $this->aeas->setCollectionAttributeDatetimeFormat(
            $filtered,
            ['created_at', 'last_session'],
            "custom",
            "d/m/y"
        );

        return Inertia::render($this->aeas->package . '/Users/Users', [
            'pbusers' => $filtered,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return Inertia::render($this->aeas->package . '/Users/CreateUser');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')],
            'password' => ['required'],
        ])->validate();

        try {
            $user = PbUser::create($request->all());

            $request->session()->flash('flash.banner', 'User Created Successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');

            return redirect()->route('users.show', $user);
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'User couldn\'t be created!');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->route('users.create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function show(int $id): InertiaResponse
    {
        $user = PbUser::find($id);

        return Inertia::render($this->aeas->package . '/Users/ShowUser', [
            'pbuser' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function edit(int $id): InertiaResponse
    {
        $user = PbUser::find($id);
        return Inertia::render($this->aeas->package . '/Users/EditUser', [
            'pbuser' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($id)],
        ])->validate();

        $user = PbUser::find($id);
        try {
            $user->update($request->all());

            $request->session()->flash('flash.banner', 'User Created Successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'User couldn\'t be updated!');
            $request->session()->flash('flash.bannerStyle', 'danger');
        }

        return redirect()->route('users.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = PbUser::find($id);
        try {
            $user->delete();

            $request->session()->flash('flash.banner', 'User deleted successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'User couldn\'t be deleted!');
            $request->session()->flash('flash.bannerStyle', 'danger');
        }

        return redirect()->route('users.index');
    }
}
