<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->logger = Log::build([
            "driver" => "single",
            "path" => storage_path('logs/app.log'),
        ]);
    }

    public function login(): View
    {
        return view('login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'auth_failed' => __('auth.failed')
            ])->onlyInput('username');
        }

        $this->logger->info("User Login", [
            "id" => Auth::user()->uuid,
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "role" => Auth::user()->role_id
        ]);

        $request->session()->regenerate();

        return redirect()->intended();
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->logger->info("User Logout", [
            "id" => Auth::user()->uuid,
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "role" => Auth::user()->role_id
        ]);

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
