<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
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
    protected $redirectTo = '/new_project'; //RouteServiceProvider::HOME;

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

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('pages.general.auth.login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        // Cek status user setelah login berhasil
        if ($user->status === 'pending') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda masih menunggu persetujuan Administrator. Silakan tunggu atau hubungi Administrator.',
            ]);
        }

        if ($user->status === 'rejected') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda telah ditolak oleh Administrator. Silakan hubungi Administrator untuk informasi lebih lanjut.',
            ]);
        }

        if ($user->status === 'approved') {
            return redirect()->intended($this->redirectPath());
        }

        // Default fallback
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'username' => 'Terjadi masalah dengan status akun Anda. Silakan hubungi Administrator.',
        ]);
    }
}
