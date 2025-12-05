<?php

namespace App\Http\Controllers\CustomerAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
   public function showResetForm(Request $request, $token)
{
    return view('customer.auth.reset-password', [
        'token' => $token,
        'email' => $request->email,
    ]);
}


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),

            function ($customer, $password) {
                $customer->password = bcrypt($password);
                $customer->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                ? redirect()->route('customer.login')->with('success', 'Password updated successfully.')
                : back()->withErrors(['email' => __($status)]);
    }
}
