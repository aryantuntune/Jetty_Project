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
    // Only track session if session_id column exists
    if (\Schema::hasColumn('users', 'session_id')) {
        // Always override previous session id
        $newSessionId = session()->getId();

        // If there is an old session, destroy it
        if ($user->session_id && $user->session_id !== $newSessionId) {
            // Remove old session from session table (optional)
            \DB::table('sessions')->where('id', $user->session_id)->delete();
        }

        // Save new session ID
        $user->session_id = $newSessionId;
        $user->save();
    }

    if ($user->role_id == 5) {
        return redirect()->route('verify.index');
    }
}

public function logout(Request $request)
{
    $user = Auth::user();
    if ($user && \Schema::hasColumn('users', 'session_id')) {
        $user->session_id = null; // clear session
        $user->save();
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}


}
