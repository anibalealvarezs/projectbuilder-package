<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\User;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbUserController extends Controller
{
    protected $aeas;
    protected $name;
    protected $table;

    function __construct()
    {
        $this->aeas = new AeasHelpers();
        $this->name = "users";
        $user = new PbUser();
        $this->table = $user->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $users = PbUser::with('country', 'city', 'lang')->latest()->paginate(5);
        $filtered = $users->map(function ($user) {
            return $user->only(['id', 'name', 'email', 'last_session', 'created_at', 'country', 'city', 'lang']);
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
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'email' => ['required', 'max:50', 'email', Rule::unique($this->table)],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }
            $request->session()->flash('flash.banner', $current);
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        } else {

            try {
                if ($user = PbUser::create($request->all())) {
                    $user->language_id = $request->input('lang');
                    $user->country_id = $request->input('country');
                    $user->save();
                }

                $request->session()->flash('flash.banner', 'User Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'User could not be created!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->back()->withInput();
            }
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
        $user = PbUser::with('country', 'city', 'lang')->find($id);

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
        $user = PbUser::with('country', 'city', 'lang')->find($id);
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
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($id)],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }
            $request->session()->flash('flash.banner', $current);
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        } else {

            $user = PbUser::find($id);
            try {
                if ($request->input('password') == "") {
                    unset($user->password);
                }
                $user->language_id = $request->input('lang');
                $user->country_id = $request->input('country');
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->save();

                $request->session()->flash('flash.banner', 'User Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'User could not be updated!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->back()->withInput();
            }
        }
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

            return redirect()->route($this->name.'.index')->withInput();
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'User could not be deleted!');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        }
    }
}
