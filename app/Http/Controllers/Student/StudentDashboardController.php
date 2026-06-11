<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    private function getTargetStudent()
    {
        $user = Auth::user();
        if ($user->rola === 'rodzic') {
            return \App\Models\User::where('rola', 'uczen')
                ->where('parent_id', $user->id)
                ->first();
        }
        return $user;
    }

    public function grades()
    {
        $student = $this->getTargetStudent();
        if (!$student || !$student->school_class_id) {
            return view('student.grades', ['subjects' => [], 'student' => $student]);
        }

        $subjects = Subject::with(['lessons.grades' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }])->get();

        return view('student.grades', compact('subjects', 'student'));
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
            return view('student.timetable', ['lessons' => [], 'student' => $student]);
        }

        $lessons = Lesson::where('school_class_id', $student->school_class_id)
            ->with(['subject', 'teacher'])
            ->orderBy('lesson_date')
            ->orderBy('lesson_number')
            ->get();

        return view('student.timetable', compact('lessons', 'student'));
    }
}