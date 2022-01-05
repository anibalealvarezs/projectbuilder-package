<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Anibalealvarezs\Projectbuilder\PbUser as User;
use Session;

class PbLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        User::current()->update(['last_session'=>Session::getId()]);

        return redirect('/dashboard');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
