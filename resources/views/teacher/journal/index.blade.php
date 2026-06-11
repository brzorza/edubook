@extends('layouts.app')

@section('title', 'Dziennik Lekcyjny - EduBook')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dziennik Klasowy</h1>
        <p class="text-sm text-gray-500 mt-1">Wybierz zajęcia z planu lekcji, aby uzupełnić temat oraz sprawdzić obecność.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200 h-fit">
            <h2 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2"><i class="fa-solid fa-calendar-day mr-2 text-blue-600"></i>Rozpocznij lekcję</h2>
            <form action="{{ route('teacher.journal.create') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Klasa</label>
                    <select name="school_class_id" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none text-sm">
                        @foreach($schoolClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Przedmiot</label>
                    <select name="subject_id" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none text-sm">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Godzina lekcyjna</label>
                    <select name="lesson_number" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none text-sm">
                        <option value="1">1. Lekcja (08:00 - 08:45)</option>
                        <option value="2">2. Lekcja (08:55 - 09:40)</option>
                        <option value="3">3. Lekcja (09:50 - 10:35)</option>
                        <option value="4">4. Lekcja (10:50 - 11:35)</option>
                        <option value="5">5. Lekcja (11:45 - 12:30)</option>
                        <option value="6">6. Lekcja (12:40 - 13:25)</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white p-2.5 rounded font-bold hover:bg-blue-700 transition shadow-sm text-sm">
                    Otwórz kartę lekcji
                </button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2"><i class="fa-solid fa-clock-history mr-2 text-purple-600"></i>Lekcje przeprowadzone dzisiaj</h2>
            
            <div class="space-y-3">
                @forelse($todayLessons as $lesson)
                    <div class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50">
                        <div>
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Lekcja {{ $lesson->lesson_number }}</span>
                            <h4 class="font-bold text-gray-800 mt-1">{{ $lesson->subject->name }} — klasa {{ $lesson->schoolClass->name }}</h4>
                            <p class="text-sm text-gray-600 font-medium italic mt-0.5">Temat: {{ $lesson->subject_title }}</p>
                        </div>
                        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-2">
                            <span class="text-xs text-gray-400 font-mono">Zapisano: {{ $lesson->created_at->format('H:i') }}</span>
                            <a href="{{ route('teacher.grades.lesson', $lesson->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-3 py-1.5 rounded transition shadow-sm whitespace-nowrap">
                                <i class="fa-solid fa-star mr-1"></i> Oceny
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                        Nie zarejestrowano jeszcze żadnej lekcji w dniu dzisiejszym.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection