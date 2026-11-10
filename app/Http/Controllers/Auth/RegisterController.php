<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Tampilkan halaman register (sama layout dengan login)
     */
    public function showRegistrationForm()
    {
        return view('pages.general.auth.register');
    }

    /**
     * Proses register
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['nullable', 'in:user'],
        ]);

        // set name default sama dengan username jika kolom name tidak ada di form
        $user = User::create([
            'username' => $data['username'],
            'name' => $data['username'], // default name sama dengan username
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
            'status' => 'pending', // status pending, menunggu approval admin
            'avatar_url' => 'images/avatar-default.svg'
        ]);
        
        return redirect()->route('register.success')->with([
            'username' => $user->username,
            'email' => $user->email
        ]);
    }
    
    public function registerSuccess()
    {
        if (!session()->has('username')) {
            return redirect()->route('register');
        }

        return view('pages.general.auth.register-success');
    }
}
