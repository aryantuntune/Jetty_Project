<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/ticket-entry';

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

    protected function authenticated(Request $request, $user)
{
    // Current session ID
    $currentSession = session()->getId();

    // If user already has a session and it's different => block login
    if ($user->session_id && $user->session_id !== $currentSession) {
        Auth::logout();

        return redirect('/login')->withErrors([
            'email' => 'This account is already logged in on another device.'
        ]);
    }

    // Store new session ID
    $user->session_id = $currentSession;
    $user->save();
}

public function logout(Request $request) 
{
    $user = Auth::user();
    if ($user) {
        $user->session_id = null; // clear session
        $user->save();
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}


}
