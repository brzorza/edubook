<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->update(['last_activity' => now()]);

            if ($user->czy_pierwsze_logowanie) {
                return redirect()->route('password.change');
            }

            return $this->redirectBasedOnRole($user->rola);
        }

        return back()->withErrors([
            'email' => 'Błędny adres e-mail lub hasło.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function redirectBasedOnRole(string $rola)
    {
        return match ($rola) {
            'admin' => redirect('/admin/dashboard'),
            'nauczyciel' => redirect('/teacher/journal'),
            'uczen', 'rodzic' => redirect('/student/grades'),
            default => redirect('/'),
        };
    }
}