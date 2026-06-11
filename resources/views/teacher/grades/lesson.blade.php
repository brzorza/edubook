@extends('layouts.app')

@section('title', 'Ocenianie Lekcji - EduBook')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('teacher.journal.index') }}" class="text-sm text-blue-600 hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Powrót do dziennika</a>
    </div>

    <div class="bg-slate-800 text-white p-4 md:p-6 rounded-t-lg shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <span class="bg-blue-600 text-white text-xs uppercase font-bold px-2.5 py-0.5 rounded">Lekcja {{ $lesson->lesson_number }}</span>
                <h1 class="text-xl md:text-2xl font-bold mt-1">{{ $lesson->subject->name }} — Klasa {{ $lesson->schoolClass->name }}</h1>
                <p class="text-sm text-slate-300 mt-0.5">Temat: {{ $lesson->subject_title }}</p>
            </div>
            <div class="text-left sm:text-right text-sm">
                <p class="text-slate-400">Data zrealizowania</p>
                <p class="font-mono font-bold text-base">{{ $lesson->lesson_date }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 border border-green-200 shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-b-lg shadow-sm border-x border-b border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs font-bold border-b border-gray-200 uppercase tracking-wider">
                        <th class="p-3">Uczeń</th>
                        <th class="p-3">Wystawione Oceny (Waga / Typ)</th>
                        <th class="p-3 text-right">Akcja</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @foreach($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 font-semibold text-gray-800 whitespace-nowrap">
                                {{ $student->nazwisko }} {{ $student->imie }}
                            </td>
                            <td class="p-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($student->grades as $grade)
                                        <div class="inline-flex items-center bg-amber-50 border border-amber-200 rounded pl-2.5 pr-1 py-1 text-xs font-bold text-amber-800 shadow-sm">
                                            <span class="text-sm mr-1.5">{{ $grade->value }}</span>
                                            <span class="text-[10px] text-amber-600 font-normal mr-2">waga: {{ $grade->weight }} ({{ $grade->type_description }})</span>
                                            <button onclick="openEditGradeModal('{{ $grade->id }}', '{{ $grade->value }}', '{{ $grade->weight }}', '{{ $grade->type_description }}')" class="text-gray-400 hover:text-blue-600 transition p-0.5">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form action="{{ route('teacher.grades.destroy', $grade->id) }}" method="POST" class="inline" onsubmit="return confirm('Usunąć tę ocenę?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition p-0.5">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                    @if($student->grades->isEmpty())
                                        <span class="text-xs text-gray-400 italic">Brak ocen z tej lekcji</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <button onclick="openAddGradeModal('{{ $student->id }}', '{{ $student->imie }} {{ $student->nazwisko }}')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded transition shadow-sm">
                                    <i class="fa-solid fa-plus mr-1"></i> Dodaj
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="add_grade_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center p-4 z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h3 class="text-lg font-bold mb-1 text-gray-800 border-b pb-2">Wystaw nową ocenę</h3>
        <p class="text-xs text-gray-500 mb-4">Uczeń: <span id="add_grade_student_name" class="font-bold text-gray-700"></span></p>
        <form action="{{ route('teacher.grades.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
            <input type="hidden" name="student_id" id="add_grade_student_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Ocena (1-6)</label>
                <select name="value" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Waga oceny (1-10)</label>
                <input type="number" name="weight" min="1" max="10" value="1" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Opis typu oceny</label>
                <input type="text" name="type_description" required placeholder="np. Sprawdzian, Kartkówka, Odpowiedź" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="closeAddGradeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded font-semibold hover:bg-gray-400 text-sm">Anuluj</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-semibold hover:bg-blue-700 text-sm">Zatwierdź</button>
            </div>
        </form>
    </div>
</div>

<div id="edit_grade_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center p-4 z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Edycja oceny</h3>
        <form id="edit_grade_form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Ocena (1-6)</label>
                <select name="value" id="edit_grade_value" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Waga oceny (1-10)</label>
                <input type="number" name="weight" id="edit_grade_weight" min="1" max="10" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Opis typu oceny</label>
                <input type="text" name="type_description" id="edit_grade_description" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
            </div>

            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="closeEditGradeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded font-semibold hover:bg-gray-400 text-sm">Anuluj</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-semibold hover:bg-blue-700 text-sm">Zapisz zmiany</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddGradeModal(studentId, studentName) {
        document.getElementById('add_grade_student_id').value = studentId;
        document.getElementById('add_grade_student_name').innerText = studentName;
        document.getElementById('add_grade_modal').classList.remove('hidden');
        document.getElementById('add_grade_modal').classList.add('flex');
    }

    function closeAddGradeModal() {
        document.getElementById('add_grade_modal').classList.add('hidden');
        document.getElementById('add_grade_modal').classList.remove('flex');
    }

    function openEditGradeModal(gradeId, value, weight, description) {
        document.getElementById('edit_grade_form').action = '/teacher/grades/' + gradeId;
        document.getElementById('edit_grade_value').value = value;
        document.getElementById('edit_grade_weight').value = weight;
        document.getElementById('edit_grade_description').value = description;
        document.getElementById('edit_grade_modal').classList.remove('hidden');
        document.getElementById('edit_grade_modal').classList.add('flex');
    }

    function closeEditGradeModal() {
        document.getElementById('edit_grade_modal').classList.add('hidden');
        document.getElementById('edit_grade_modal').classList.remove('flex');
    }
</script>
@endsection