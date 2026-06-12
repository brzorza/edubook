<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Lesson;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Message;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin1 = User::create([
            'imie' => 'Jan',
            'nazwisko' => 'Administrator',
            'email' => 'admin@edubook.pl',
            'password' => Hash::make('asdasdasd'),
            'rola' => 'admin',
            'czy_pierwsze_logowanie' => false,
        ]);

        $admin2 = User::create([
            'imie' => 'Anna',
            'nazwisko' => 'Administrator',
            'email' => 'admin2@edubook.pl',
            'password' => Hash::make('asdasdasd'),
            'rola' => 'admin',
            'czy_pierwsze_logowanie' => false,
        ]);

        $subjectNames = ['Matematyka', 'Język Polski', 'Język Angielski', 'Historia', 'Geografia'];
        $subjects = [];
        foreach ($subjectNames as $name) {
            $subjects[] = Subject::create([
                'name' => $name,
                'description' => "Przedmiot szkolny: $name",
            ]);
        }

        $teachers = [];
        $teacherNames = [
            ['Adam', 'Nowak'],
            ['Beata', 'Kowalska'],
            ['Cezary', 'Wiśniewski'],
            ['Dorota', 'Wójcik'],
            ['Edward', 'Kamiński']
        ];
        
        foreach ($teacherNames as $index => $names) {
            $teacher = User::create([
                'imie' => $names[0],
                'nazwisko' => $names[1],
                'email' => 'nauczyciel' . ($index + 1) . '@edubook.pl',
                'password' => Hash::make('asdasdasd'),
                'rola' => 'nauczyciel',
                'czy_pierwsze_logowanie' => false,
            ]);
            $teachers[] = $teacher;
            $subjects[$index]->teachers()->attach($teacher->id);
        }

        $classNames = ['1A', '2A', '3A'];
        $classes = [];
        foreach ($classNames as $name) {
            $classes[] = SchoolClass::create([
                'name' => $name,
                'school_year' => '2025/2026',
            ]);
        }

        $studentIndex = 1;
        $allStudents = [];
        $allParents = [];

        foreach ($classes as $classIndex => $class) {
            for ($i = 1; $i <= 5; $i++) {
                $parent = User::create([
                    'imie' => 'Rodzic',
                    'nazwisko' => "Ucznia $studentIndex",
                    'email' => "rodzic$studentIndex@edubook.pl",
                    'password' => Hash::make('asdasdasd'),
                    'rola' => 'rodzic',
                    'czy_pierwsze_logowanie' => false,
                ]);
                $allParents[] = $parent;

                $student = User::create([
                    'imie' => 'Uczeń',
                    'nazwisko' => "Numer $studentIndex",
                    'email' => "uczen$studentIndex@edubook.pl",
                    'password' => Hash::make('asdasdasd'),
                    'rola' => 'uczen',
                    'parent_id' => $parent->id,
                    'school_class_id' => $class->id,
                    'czy_pierwsze_logowanie' => false,
                ]);
                $allStudents[] = $student;

                foreach ($subjects as $subject) {
                    $randomTeacher = $teachers[array_rand($teachers)];

                    $lesson = Lesson::create([
                        'subject_id' => $subject->id,
                        'school_class_id' => $class->id,
                        'teacher_id' => $randomTeacher->id,
                        'lesson_date' => now()->subDays(rand(1, 10))->format('Y-m-d'),
                        'lesson_number' => rand(1, 6),
                        'subject_title' => 'Temat lekcji z przedmiotu ' . $subject->name,
                    ]);

                    for ($g = 1; $g <= 2; $g++) {
                        Grade::create([
                            'student_id' => $student->id,
                            'lesson_id' => $lesson->id,
                            'teacher_id' => $randomTeacher->id,
                            'value' => rand(2, 5),
                            'weight' => rand(1, 3),
                            'type_description' => 'Sprawdzian / Aktywność',
                        ]);
                    }

                    $statusOptions = ['Ob', 'Nb', 'Sp', 'Zw'];
                    Attendance::create([
                        'student_id' => $student->id,
                        'lesson_id' => $lesson->id,
                        'status' => $statusOptions[array_rand($statusOptions)],
                    ]);
                }

                $studentIndex++;
            }
        }

        foreach ($classes as $class) {
            foreach ($subjects as $index => $subject) {
                Lesson::create([
                    'subject_id' => $subject->id,
                    'school_class_id' => $class->id,
                    'teacher_id' => $teachers[$index]->id,
                    'lesson_date' => now()->addDays(rand(1, 5))->format('Y-m-d'),
                    'lesson_number' => $index + 1,
                    'subject_title' => 'Nadchodzące zajęcia z wprowadzenia do ' . $subject->name,
                ]);
            }
        }
        
        for ($m = 0; $m < 5; $m++) {
            Message::create([
                'sender_id' => $teachers[array_rand($teachers)]->id,
                'recipient_id' => $allParents[array_rand($allParents)]->id,
                'title' => 'Informacja w sprawie postępów w nauce',
                'content' => 'Dzień dobry, zwracam się z prośbą o weryfikację ocen z ostatniego sprawdzianu. W razie pytań zapraszam na konsultacje.',
                'read_at' => null,
            ]);
        }

        $announcement1 = Announcement::create([
            'user_id' => $admin1->id,
            'title' => 'Przerwa świąteczna',
            'content' => 'Informujemy, że w dniach od 23 do 31 grudnia szkoła będzie zamknięta.',
            'target_all' => true,
        ]);

        $announcement2 = Announcement::create([
            'user_id' => $admin2->id,
            'title' => 'Wycieczka integracyjna klasy 1A',
            'content' => 'Przypominamy o konieczności dostarczenia zgód na wyjazd do końca bieżącego tygodnia.',
            'target_all' => false,
        ]);

        DB::table('announcement_school_class')->insert([
            'announcement_id' => $announcement2->id,
            'school_class_id' => $classes[0]->id,
        ]);
    }
}