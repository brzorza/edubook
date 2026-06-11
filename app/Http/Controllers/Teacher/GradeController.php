<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function showLessonGrades(Lesson $lesson)
    {
        $lesson->load(['schoolClass', 'subject']);
        
        $students = User::where('rola', 'uczen')
            ->where('school_class_id', $lesson->school_class_id)
            ->with(['grades' => function ($query) use ($lesson) {
                $query->where('lesson_id', $lesson->id);
            }])
            ->orderBy('nazwisko')
            ->get();

        return view('teacher.grades.lesson', compact('lesson', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|integer|min:1|max:6',
            'weight' => 'required|integer|min:1|max:10',
            'type_description' => 'required|string|max:255',
            'lesson_id' => 'required|exists:lessons,id',
            'student_id' => 'required|exists:users,id',
        ]);

        Grade::create([
            'value' => $validated['value'],
            'weight' => $validated['weight'],
            'type_description' => $validated['type_description'],
            'lesson_id' => $validated['lesson_id'],
            'student_id' => $validated['student_id'],
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Ocena została pomyślnie wystawiona.');
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'value' => 'required|integer|min:1|max:6',
            'weight' => 'required|integer|min:1|max:10',
            'type_description' => 'required|string|max:255',
        ]);

        $grade->update($validated);

        return redirect()->back()->with('success', 'Ocena została zaktualizowana.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->back()->with('success', 'Ocena została usunięta.');
    }
}