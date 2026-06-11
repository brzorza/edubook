@extends('layouts.app')

@section('title', 'Moja Frekwencja - EduBook')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Historia Frekwencji</h1>
        <p class="text-sm text-gray-500 mt-1">Uczeń: <span class="font-bold text-gray-700">{{ $student->imie }} {{ $student->nazwisko }}</span></p>
    </div>

    @if(empty($attendances) || count($attendances) === 0)
        <div class="bg-gray-50 border border-gray-200 text-gray-500 text-center py-12 rounded-lg">
            <i class="fa-solid fa-user-check text-3xl mb-2 text-gray-300 block"></i>
            Brak zarejestrowanych wpisów frekwencji.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs font-bold border-b border-gray-200 uppercase tracking-wider">
                            <th class="p-3">Data lekcji</th>
                            <th class="p-3">Godzina</th>
                            <th class="p-3">Przedmiot</th>
                            <th class="p-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @foreach($attendances as $attendance)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3 font-mono text-gray-600 whitespace-nowrap">{{ $attendance->lesson->lesson_date }}</td>
                                <td class="p-3 text-gray-500 whitespace-nowrap">Lekcja {{ $attendance->lesson->lesson_number }}</td>
                                <td class="p-3 font-semibold text-gray-800 whitespace-nowrap">{{ $attendance->lesson->subject->name }}</td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        {{ $attendance->status === 'Ob' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $attendance->status === 'Nb' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $attendance->status === 'Sp' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $attendance->status === 'Zw' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">
                                        @if($attendance->status === 'Ob') Obecny
                                        @elseif($attendance->status === 'Nb') Nieobecny
                                        @elseif($attendance->status === 'Sp') Spóźnienie
                                        @elseif($attendance->status === 'Zw') Usprawiedliwione
                                        @endif
                                    </span>
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