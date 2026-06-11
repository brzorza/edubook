@extends('layouts.app')

@section('title', 'Plan Lekcji - EduBook')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Plan Zrealizowanych Lekcji</h1>
        <p class="text-sm text-gray-500 mt-1">Uczeń: <span class="font-bold text-gray-700">{{ $student->imie }} {{ $student->nazwisko }}</span></p>
    </div>

    @if(empty($lessons) || count($lessons) === 0)
        <div class="bg-gray-50 border border-gray-200 text-gray-500 text-center py-12 rounded-lg">
            <i class="fa-solid fa-calendar-xmark text-3xl mb-2 text-gray-300 block"></i>
            Brak lekcji w planie dla Twojej klasy.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lessons as $lesson)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="bg-slate-700 text-white p-3 flex justify-between items-center">
                        <span class="text-xs font-bold uppercase tracking-wider bg-slate-600 px-2 py-0.5 rounded">Lekcja {{ $lesson->lesson_number }}</span>
                        <span class="text-xs font-mono">{{ $lesson->lesson_date }}</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 truncate">{{ $lesson->subject->name }}</h3>
                        <p class="text-sm text-gray-600 font-medium italic mt-1 truncate">Temat: {{ $lesson->subject_title }}</p>
                        <div class="border-t mt-3 pt-2 text-xs text-gray-400 flex items-center">
                            <i class="fa-solid fa-chalkboard-user mr-1.5"></i>
                            Prowadzący: {{ $lesson->teacher->imie }} {{ $lesson->teacher->nazwisko }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection