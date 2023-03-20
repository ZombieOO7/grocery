<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Route;

class AdminLoginController extends Controller
{

    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        // Attempt to log the user in
        $request->email = strtolower($request->email);
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
             if ($request->remember == "true") {
                setcookie("username", $request->email, time() + 60 * 60 * 24 * 365);
                setcookie("password", $request->password, time() + 60 * 60 * 24 * 365);
                echo "Cookies Set Successfuly";
            }
            // if successful, then redirect to their intended location
            return redirect()->intended(route('admin_dashboard'));
        }
        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'))->with('error', 'These credentials do not match our records.');
    }

    // public function logout(Request $request)
    // {
    //     Auth::guard('admin')->logout();
    //     $request->session()->regenerate(true);
    //     return redirect('admin/login');
    // }
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/admin/login');
    }
    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function loggedOut(Request $request)
    {
        //
    }
}