@extends('layouts.app')

@section('title', 'Statystyki i Raporty - EduBook')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Centrum Analityczne</h1>
            <p class="text-sm text-gray-500 mt-1">Generowanie statystyk, zestawień średnich oraz poziomów frekwencji.</p>
        </div>
        <form action="{{ route('analytics.index') }}" method="GET" class="w-full sm:w-auto">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Filtruj według klasy</label>
            <select name="school_class_id" onchange="this.form.submit()" class="w-full sm:w-64 rounded border border-gray-300 p-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">-- Cała Szkoła (Przegląd Klas) --</option>
                @foreach($schoolClasses as $class)
                    <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>Klasa {{ $class->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(empty($selectedClassId))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($classStats as $stat)
                <div class="bg-white p-5 border border-gray-200 rounded-lg shadow-sm flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Klasa {{ $stat['name'] }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Podsumowanie semestralne oddziału</p>
                    </div>
                    <div class="flex space-x-6 text-right">
                        <div>
                            <span class="block text-xs text-gray-400 font-medium uppercase tracking-wider">Średnia</span>
                            <span class="text-xl font-extrabold text-blue-600">{{ $stat['average'] }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 font-medium uppercase tracking-wider">Frekwencja</span>
                            <span class="text-xl font-extrabold text-emerald-600">{{ $stat['attendance'] }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b font-bold text-gray-700">
                Zestawienie uczniów klasy
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100/70 text-gray-600 text-xs font-bold border-b border-gray-200 uppercase tracking-wider">
                            <th class="p-4">Uczeń</th>
                            <th class="p-4 text-center">Średnia ważona ocen</th>
                            <th class="p-4 text-right">Wskaźnik obecności</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @forelse($studentStats as $stat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold text-gray-800 whitespace-nowrap">{{ $stat['name'] }}</td>
                                <td class="p-4 text-center font-mono font-semibold text-blue-600 text-base">{{ $stat['average'] }}</td>
                                <td class="p-4 text-right whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded font-mono font-bold text-xs {{ $stat['attendance'] >= 85 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $stat['attendance'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400">Brak zarejestrowanych uczniów w tej klasie.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection