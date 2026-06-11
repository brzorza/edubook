<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('asdasdasd');

        $administrators = [
            [
                'imie' => 'Jan',
                'nazwisko' => 'Kowalski',
                'email' => 'admin1@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'admin',
                'czy_pierwsze_logowanie' => false,
            ],
            [
                'imie' => 'Anna',
                'nazwisko' => 'Nowak',
                'email' => 'admin2@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'admin',
                'czy_pierwsze_logowanie' => false,
            ],
        ];

        foreach ($administrators as $adminData) {
            User::create($adminData);
        }

        $teachers = [
            [
                'imie' => 'Mariusz',
                'nazwisko' => 'Wiśniewski',
                'email' => 'mariusz.wisniewski@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'nauczyciel',
                'czy_pierwsze_logowanie' => false,
            ],
            [
                'imie' => 'Małgorzata',
                'nazwisko' => 'Wójcik',
                'email' => 'malgorzata.wojcik@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'nauczyciel',
                'czy_pierwsze_logowanie' => false,
            ],
            [
                'imie' => 'Andrzej',
                'nazwisko' => 'Kamiński',
                'email' => 'andrzej.kaminski@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'nauczyciel',
                'czy_pierwsze_logowanie' => false,
            ],
        ];

        foreach ($teachers as $teacherData) {
            User::create($teacherData);
        }

        $parents = [
            [
                'imie' => 'Piotr',
                'nazwisko' => 'Zieliński',
                'email' => 'piotr.zielinski@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'rodzic',
                'czy_pierwsze_logowanie' => false,
            ],
            [
                'imie' => 'Krystyna',
                'nazwisko' => 'Szymańska',
                'email' => 'krystyna.szymanska@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'rodzic',
                'czy_pierwsze_logowanie' => false,
            ],
        ];

        foreach ($parents as $parentData) {
            User::create($parentData);
        }

        $students = [
            [
                'imie' => 'Kacper',
                'nazwisko' => 'Zieliński',
                'email' => 'kacper.zielinski@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'uczen',
                'czy_pierwsze_logowanie' => false,
            ],
            [
                'imie' => 'Aleksandra',
                'nazwisko' => 'Szymańska',
                'email' => 'aleksandra.szymanska@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'uczen',
                'czy_pierwsze_logowanie' => false,
            ],
            [
                'imie' => 'Michał',
                'nazwisko' => 'Szymański',
                'email' => 'michal.szymanski@edubook.pl',
                'password' => $defaultPassword,
                'rola' => 'uczen',
                'czy_pierwsze_logowanie' => false,
            ],
        ];

        foreach ($students as $studentData) {
            User::create($studentData);
        }
    }
}