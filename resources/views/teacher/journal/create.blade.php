@extends('layouts.app')

@section('title', 'Rejestracja Lekcji - EduBook')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('teacher.journal.index') }}" class="text-sm text-blue-600 hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Anuluj i wróć do dziennika</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-slate-800 text-white p-4 md:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-blue-600 text-white text-xs uppercase font-bold px-3 py-1 rounded">Lekcja nr {{ $lessonNumber }}</span>
                    <h1 class="text-xl md:text-2xl font-bold mt-2">{{ $subject->name }} — Klasa {{ $schoolClass->name }}</h1>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs text-slate-300">Data lekcji</p>
                    <p class="font-mono font-bold text-base md:text-lg">{{ $lessonDate }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('teacher.journal.store') }}" method="POST" class="p-4 md:p-6 space-y-6">
            @csrf
            <input type="hidden" name="school_class_id" value="{{ $schoolClass->id }}">
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <input type="hidden" name="lesson_number" value="{{ $lessonNumber }}">
            <input type="hidden" name="lesson_date" value="{{ $lessonDate }}">

            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Temat lekcji</label>
                <input type="text" name="subject_title" required placeholder="Wpisz realizowany temat zajęć..." 
                       class="w-full bg-white rounded border border-gray-300 p-3 text-gray-800 font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('subject_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3 border-b pb-1">Sprawdzenie frekwencji uczniów</h3>
                
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <div class="inline-block min-w-full align-middle p-4 md:p-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 text-xs font-bold border-b border-gray-200 uppercase tracking-wider">
                                    <th class="p-3">Uczeń</th>
                                    <th class="p-3 text-center">Ob (Obecny)</th>
                                    <th class="p-3 text-center">Nb (Nieobecny)</th>
                                    <th class="p-3 text-center">Sp (Spóźnienie)</th>
                                    <th class="p-3 text-center">Zw (Zwolniony)</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-3 font-semibold text-gray-800 whitespace-nowrap">{{ $student->nazwisko }} {{ $student->imie }}</td>
                                        
                                        <td class="p-3 text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="Ob" checked
                                                   class="w-5 h-5 sm:w-4 sm:h-4 text-green-600 focus:ring-green-500 border-gray-300">
                                        </td>
                                        
                                        <td class="p-3 text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="Nb"
                                                   class="w-5 h-5 sm:w-4 sm:h-4 text-red-600 focus:ring-red-500 border-gray-300">
                                        </td>
                                        
                                        <td class="p-3 text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="Sp"
                                                   class="w-5 h-5 sm:w-4 sm:h-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                        </td>
                                        
                                        <td class="p-3 text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="Zw"
                                                   class="w-5 h-5 sm:w-4 sm:h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-gray-400">Brak przypisanych uczniów do tej klasy.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="border-t pt-4 flex justify-end">
                <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded shadow-sm transition uppercase text-sm tracking-wider">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Zatwierdź i zapisz lekcję
                </button>
            </div>
        </form>
    </div>
</div>
@endsection