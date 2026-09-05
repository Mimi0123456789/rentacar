<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        if ($user->isCollaborateur()) {
            return redirect()->route('collaborateur.dashboard');
        }

        if ($user->isGestion() || $user->isAdmin()) {
            return redirect()->route('planning.index');
        }

        return redirect()->intended('/');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
