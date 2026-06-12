<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    private function getTargetStudent()
    {
        $user = Auth::user();
        if ($user->rola === 'rodzic') {
            return User::where('rola', 'uczen')
                ->where('parent_id', $user->id)
                ->first();
        }
        return $user;
    }

    public function grades()
    {
        $student = $this->getTargetStudent();

        if (!$student) {
            abort(403, 'Do Twojego konta rodzica nie przypisano żadnego ucznia.');
        }

        $subjects = [];
        
        if ($student->school_class_id) {
            $subjects = Subject::whereHas('lessons', function($query) use ($student) {
                $query->where('school_class_id', $student->school_class_id);
            })->with(['lessons' => function($query) use ($student) {
                $query->where('school_class_id', $student->school_class_id)
                      ->with(['grades' => function($gQuery) use ($student) {
                          $gQuery->where('student_id', $student->id);
                      }]);
            }])->get();
        }

        return view('student.grades', compact('student', 'subjects'));
    }

    public function attendance()
    {
        $student = $this->getTargetStudent();
        if (!$student) {
            return view('student.attendance', ['attendances' => [], 'student' => $student]);
        }

        $attendances = Attendance::where('student_id', $student->id)
            ->with(['lesson.subject'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.attendance', compact('attendances', 'student'));
    }

    public function timetable()
    {
        $student = $this->getTargetStudent();

        if (!$student || !$student->school_class_id) {
            return view('student.timetable', ['days' => []]);
        }

        // Pobieramy lekcje i wprost sortujemy po dacie oraz rosnąco po numerze lekcji
        $lessons = Lesson::where('school_class_id', $student->school_class_id)
            ->with(['subject', 'teacher'])
            ->orderBy('lesson_date', 'asc')
            ->orderBy('lesson_number', 'asc')
            ->get();

        $polishDays = [
            1 => 'Poniedziałek',
            2 => 'Wtorek',
            3 => 'Środa',
            4 => 'Czwartek',
            5 => 'Piątek',
            6 => 'Sobota',
            7 => 'Niedziela',
        ];

        // Grupowanie: kluczem staje się format np. "Poniedziałek (15.06.2026)"
        $days = $lessons->groupBy(function ($lesson) use ($polishDays) {
            $carbonDate = Carbon::parse($lesson->lesson_date);
            $dayName = $polishDays[$carbonDate->dayOfWeekIso] ?? 'Inny dzień';
            $formattedDate = $carbonDate->format('d.m.Y');
            
            return "{$dayName} ({$formattedDate})";
        });

        return view('student.timetable', compact('days'));
    }
}