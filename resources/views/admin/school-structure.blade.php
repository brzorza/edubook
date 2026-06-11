@extends('layouts.app')

@section('title', 'Struktura Szkoły - EduBook')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Zarządzanie Strukturą Szkoły</h1>
        <p class="text-sm text-gray-500 mt-1">Konfiguracja oddziałów klasowych, przedmiotów oraz mapowanie zasobów ludzkich.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 shadow-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 mb-6 md:mb-8">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-700 border-b pb-2">Dodaj Nową Klasę</h2>
            <form action="{{ route('admin.classes.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nazwa klasy (np. 1A)</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rok szkolny (np. 2025/2026)</label>
                    <input type="text" name="school_year" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">Utwórz klasę</button>
            </form>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-700 border-b pb-2">Dodaj Nowy Przedmiot</h2>
            <form action="{{ route('admin.subjects.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nazwa przedmiotu</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Opis (opcjonalnie)</label>
                    <textarea name="description" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white p-2 rounded font-bold hover:bg-indigo-700 transition">Dodaj przedmiot</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 mb-6 md:mb-8">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-700 border-b pb-2">Przypisz Nauczyciela do Przedmiotu</h2>
            <form action="{{ route('admin.teachers.assign') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Wybierz Przedmiot</label>
                    <select name="subject_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Wybierz Nauczyciela</label>
                    <select name="teacher_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->imie }} {{ $teacher->nazwisko }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded font-bold hover:bg-purple-700 transition">Przypisz</button>
            </form>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-700 border-b pb-2">Przypisz Ucznia do Klasy</h2>
            <form action="{{ route('admin.students.assign') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Wybierz Ucznia</label>
                    <select name="student_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->imie }} {{ $student->nazwisko }} 
                                ({{ $student->schoolClass ? $student->schoolClass->name : 'Brak klasy' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Wybierz Klasę Docelową</label>
                    <select name="school_class_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($schoolClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->school_year }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white p-2 rounded font-bold hover:bg-green-700 transition">Przypisz do klasy</button>
            </form>
        </div>
    </div>

    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
        <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-700 border-b pb-2">Aktualne Klasy w Systemie</h2>
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle p-4 md:p-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs font-bold border-b border-gray-200">
                            <th class="p-3">Nazwa Klasy</th>
                            <th class="p-3">Rok Szkolny</th>
                            <th class="p-3">Liczba Uczniów</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($schoolClasses as $class)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="p-3 font-semibold text-slate-700">{{ $class->name }}</td>
                                <td class="p-3 text-gray-500">{{ $class->school_year }}</td>
                                <td class="p-3"><span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full whitespace-nowrap">{{ $class->students_count }} uczniów</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection