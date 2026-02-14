<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepositoryInterface $authRepo)
    {
        $this->authRepository = $authRepo;
    }

    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Email is required',
            'password.required' => 'Passwrd is required'
        ]);

        $result = $this->authRepository->login($request->all());

        if ($result['success']) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard.index'))->with('success', $result['message']);
        }

        return back()->withErrors([
            'email' => $result['message']
        ])->withInput();
    }

    public function forgot()
    {
        return view('auth.forgot_password');
    }

    public function forgotPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $result = $this->authRepository->forgot($request->all());

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors([
            'email' => $result['message']
        ])->withInput();
    }

    public function resetPassword(Request $request)
    {
        $isValid = $this->authRepository->validateResetToken($request->token, $request->email);

        if (!$isValid) {
            return redirect()->route('login')->with('error', 'Reset Password Expired or Invalid Token');
        }

        return view('auth.reset_password', ['token' => $request->token, 'email' => $request->email]);
    }

    public function resetPasswordProcess(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'regex:/[0-9]/', 'confirmed']
        ], [
            'password.regex' => 'Must be 8 or more characters and contain at least 1 number'
        ]);

        $data = $request->only('email', 'token', 'password');

        $result = $this->authRepository->reset($data);

        if ($result['success']) {
            return redirect()->route('login')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function logout(Request $request)
    {
        $result = $this->authRepository->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', $result['message']);
    }
}
