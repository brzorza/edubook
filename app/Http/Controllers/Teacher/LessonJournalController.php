<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonJournalController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();

        // POPRAWKA: Zmiana z 'nazwa' na 'name'
        $schoolClasses = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        // Pobieramy dzisiejsze już zrealizowane lekcje przez nauczyciela
        $todayLessons = Lesson::where('teacher_id', $teacher->id)
            ->where('lesson_date', today()->toDateString())
            ->with(['schoolClass', 'subject'])
            ->orderBy('lesson_number')
            ->get();

        return view('teacher.journal.index', compact('schoolClasses', 'subjects', 'todayLessons'));
    }

    public function createLesson(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'lesson_number' => 'required|integer|min:1|max:10',
        ]);

        $schoolClassId = $request->input('school_class_id');
        $subjectId = $request->input('subject_id');
        $lessonNumber = $request->input('lesson_number');
        $lessonDate = today()->toDateString();

        $existingLesson = Lesson::where('school_class_id', $schoolClassId)
            ->where('lesson_date', $lessonDate)
            ->where('lesson_number', $lessonNumber)
            ->first();

        if ($existingLesson) {
            return redirect()->route('teacher.journal.index')
                ->with('error', 'Ta lekcja została już przeprowadzona i zapisana w dzienniku.');
        }

        $schoolClass = SchoolClass::findOrFail($schoolClassId);
        $subject = Subject::findOrFail($subjectId);

        $students = User::where('rola', 'uczen')
            ->where('school_class_id', $schoolClassId)
            ->orderBy('nazwisko')
            ->get();

        return view('teacher.journal.create', compact('schoolClass', 'subject', 'lessonNumber', 'students', 'lessonDate'));
    }

    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'lesson_date' => 'required|date',
            'lesson_number' => 'required|integer',
            'subject_title' => 'required|string|max:255', 
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:Ob,Nb,Sp,Zw',
        ]);

        $lesson = Lesson::create([
            'lesson_date' => $validated['lesson_date'],
            'lesson_number' => $validated['lesson_number'],
            'subject_title' => $validated['subject_title'],
            'school_class_id' => $validated['school_class_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => Auth::id(),
        ]);

        foreach ($validated['attendance'] as $studentId => $status) {
            Attendance::create([
                'status' => $status,
                'lesson_id' => $lesson->id,
                'student_id' => $studentId,
            ]);
        }

        return redirect()->route('teacher.journal.index')
            ->with('success', 'Lekcja została pomyślnie zarejestrowana, a frekwencja zapisana.');
    }
}