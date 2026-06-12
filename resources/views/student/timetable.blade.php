@extends('layouts.app')

@section('title', 'Plan Lekcji - EduBook')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Plan Lekcji</h1>

        @if($days->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
                Brak zaplanowanych lekcji dla Twojej klasy.
            </div>
        @else
            <div class="space-y-8">
                @foreach($days as $dayLabel => $dayLessons)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                        <div class="bg-blue-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">{{ $dayLabel }}</h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm font-semibold uppercase">
                                        <th class="px-6 py-3 w-20">Nr lekcji</th>
                                        <th class="px-6 py-3">Przedmiot</th>
                                        <th class="px-6 py-3">Nauczyciel</th>
                                        <th class="px-6 py-3">Temat zajęć</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
                                    @foreach($dayLessons as $lesson)
                                        <tr class="hover:bg-blue-50 transition-colors">
                                            <td class="px-6 py-4 font-bold text-blue-600 text-center bg-gray-50/50 w-20">
                                                {{ $lesson->lesson_number }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                {{ $lesson->subject->name ?? 'Brak przedmiotu' }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $lesson->teacher->imie ?? '' }} {{ $lesson->teacher->nazwisko ?? '' }}
                                            </td>
                                            <td class="px-6 py-4 italic text-gray-400">
                                                {{ $lesson->subject_title }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection