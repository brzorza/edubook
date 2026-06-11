<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Podane obecne hasło jest niepoprawne.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'czy_pierwsze_logowanie' => false,
        ]);

        return match ($user->rola) {
            'admin' => redirect('/admin/dashboard'),
            'nauczyciel' => redirect('/teacher/journal'),
            'uczen', 'rodzic' => redirect('/student/grades'),
            default => redirect('/'),
        };
    }
}