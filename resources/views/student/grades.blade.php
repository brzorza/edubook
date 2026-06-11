@extends('layouts.app')

@section('title', 'Moje Oceny - EduBook')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Karta Ocen</h1>
        <p class="text-sm text-gray-500 mt-1">Uczeń: <span class="font-bold text-gray-700">{{ $student->imie }} {{ $student->nazwisko }}</span></p>
    </div>

    @if(empty($subjects))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg">
            Brak przypisania do klasy lub brak ocen.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs font-bold border-b border-gray-200 uppercase tracking-wider">
                            <th class="p-4 w-1/3">Przedmiot</th>
                            <th class="p-4 w-2/3">Oceny (Waga / Opis)</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @foreach($subjects as $subject)
                            @php
                                $grades = $subject->lessons->flatMap->grades;
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold text-gray-800 whitespace-nowrap">
                                    {{ $subject->name }}
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($grades as $grade)
                                            <div class="group relative inline-block bg-slate-100 border border-slate-200 rounded px-3 py-1.5 text-sm font-bold text-slate-800 cursor-help shadow-sm">
                                                <span>{{ $grade->value }}</span>
                                                <span class="block text-[10px] text-slate-500 font-normal">waga: {{ $grade->weight }}</span>
                                                
                                                <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 w-48 -translate-x-1/2 scale-0 rounded bg-slate-900 p-2 text-center text-xs font-normal text-white shadow-md transition-all group-hover:scale-100">
                                                    {{ $grade->type_description }}
                                                    <div class="text-[10px] text-slate-400 mt-1">Data: {{ $grade->created_at->format('d.m.Y') }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Brak ocen</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection