<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolStructureController extends Controller
{
    public function index()
    {
        $schoolClasses = SchoolClass::withCount('students')->get();
        $subjects = Subject::with('teachers')->get();
        $teachers = User::where('rola', 'nauczyciel')->get();
        $students = User::where('rola', 'uczen')->with('schoolClass')->get();

        return view('admin.school-structure', compact('schoolClasses', 'subjects', 'teachers', 'students'));
    }

    public function storeClass(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:10'],
            'school_year' => ['required', 'string', 'max:20'],
        ]);

        SchoolClass::create($validatedData);

        return back()->with('success', 'Klasa została utworzona.');
    }

    public function updateClass(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:10'],
            'school_year' => ['required', 'string', 'max:20'],
        ]);

        $schoolClass = SchoolClass::findOrFail($id);
        $schoolClass->update($validatedData);

        return back()->with('success', 'Klasa została zaktualizowana.');
    }

    public function storeSubject(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Subject::create($validatedData);

        return back()->with('success', 'Przedmiot został dodany.');
    }

    public function assignTeacher(Request $request)
    {
        $validatedData = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
        ]);

        $subject = Subject::findOrFail($validatedData['subject_id']);
        $subject->teachers()->syncWithoutDetaching([$validatedData['teacher_id']]);

        return back()->with('success', 'Nauczyciel został przypisany do przedmiotu.');
    }

    public function assignStudent(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
        ]);
        
        $student = User::findOrFail($validatedData['student_id']);
        $student->update(['school_class_id' => $validatedData['school_class_id']]);
        // dd($student);

        return back()->with('success', 'Uczeń został przypisany do klasy.');
    }
}