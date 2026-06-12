<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $selectedRole = $request->input('role');

        $usersQuery = User::query();
        if ($selectedRole) {
            $usersQuery->where('rola', $selectedRole);
        } else {
            $usersQuery->whereIn('rola', ['nauczyciel', 'rodzic', 'uczen']);
        }

        $users = $usersQuery->with(['schoolClass', 'parent'])->orderBy('nazwisko')->get();
        $schoolClasses = SchoolClass::orderBy('name')->get();
        
        // Pobieramy wszystkich rodziców, żeby admin mógł ich wybrać z listy rozwijanej
        $parents = User::where('rola', 'rodzic')->orderBy('nazwisko')->get();

        return view('admin.users', compact('users', 'schoolClasses', 'selectedRole', 'parents'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'imie' => ['required', 'string', 'max:255'],
            'nazwisko' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'rola' => ['required', 'in:nauczyciel,rodzic,uczen'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'parent_id' => ['nullable', 'exists:users,id'], // walidacja nowego pola
        ]);

        $generatedPassword = Str::random(8);
        $validatedData['password'] = Hash::make($generatedPassword);
        $validatedData['czy_pierwsze_logowanie'] = true;

        // Jeśli rejestrujemy kogoś innego niż uczeń, zerujemy parent_id na wszelki wypadek
        if ($validatedData['rola'] !== 'uczen') {
            $validatedData['parent_id'] = null;
        }

        User::create($validatedData);

        return back()->with('user_created_success', [
            'name' => $validatedData['imie'] . ' ' . $validatedData['nazwisko'],
            'email' => $validatedData['email'],
            'password' => $generatedPassword,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'imie' => ['required', 'string', 'max:255'],
            'nazwisko' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'parent_id' => ['nullable', 'exists:users,id'], // walidacja nowego pola przy edycji
        ]);

        // Jeśli zmieniono rolę na inną niż uczeń (lub użytkownik nim nie jest), usuwamy rodzica
        if ($user->rola !== 'uczen') {
            $validatedData['parent_id'] = null;
        }

        $user->update($validatedData);

        return back()->with('success', 'Dane użytkownika zostały zaktualizowane.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Użytkownik został pomyślnie usunięty z systemu.');
    }
}