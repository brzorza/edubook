@extends('layouts.app')

@section('title', 'Ogłoszenia - EduBook')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Tablica Ogłoszeń Szkoły</h1>
            <p class="text-sm text-gray-500 mt-1">Komunikaty, zarządzenia i wiadomości od Dyrekcji oraz Administratorów.</p>
        </div>
        @if(in_array(Auth::user()->rola, ['admin', 'dyrekcja']))
            <button onclick="openAnnouncementModal()" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded font-bold transition text-sm whitespace-nowrap">
                <i class="fa-solid fa-plus mr-1.5"></i> Dodaj Ogłoszenie
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-200 shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
                <div class="bg-slate-50 px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="bg-slate-800 text-white text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded">
                            {{ $announcement->author->rola }}
                        </span>
                        <span class="text-sm font-bold text-slate-700">{{ $announcement->author->imie }} {{ $announcement->author->nazwisko }}</span>
                        <span class="text-gray-300 text-xs hidden sm:block">|</span>
                        @if($announcement->target_all)
                            <span class="bg-blue-100 text-blue-800 text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-full">Wszyscy</span>
                        @else
                            <div class="flex flex-wrap gap-1">
                                @foreach($announcement->schoolClasses as $class)
                                    <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Klasa {{ $class->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-end">
                        <span class="text-xs text-gray-400 font-mono">{{ $announcement->created_at->format('d.m.Y H:i') }}</span>
                        @if(in_array(Auth::user()->rola, ['admin', 'dyrekcja']))
                            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Usunąć to ogłoszenie?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition text-sm p-1"><i class="fa-solid fa-trash-can"></i></button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="p-4 md:p-5">
                    <h2 class="text-base md:text-lg font-bold text-gray-800 mb-2">{{ $announcement->title }}</h2>
                    <div class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $announcement->content }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white border rounded-lg text-gray-400 shadow-sm">
                <i class="fa-solid fa-bullhorn text-4xl text-gray-200 mb-3 block"></i>
                Aktualnie nie ma żadnych nowych ogłoszeń na tablicy.
            </div>
        @endforelse
    </div>
</div>

@if(in_array(Auth::user()->rola, ['admin', 'dyrekcja']))
<div id="announcement_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
        <div class="bg-slate-800 text-white p-4 flex justify-between items-center">
            <h3 class="font-bold text-base">Nowe Ogłoszenie</h3>
            <button onclick="closeAnnouncementModal()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('announcements.store') }}" method="POST" class="p-4 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Tytuł ogłoszenia</label>
                <input type="text" name="title" required class="mt-1 block w-full rounded border-gray-300 p-2 border text-sm focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Adresaci</label>
                <select name="target" id="target_select" onchange="toggleClassesSelector(this.value)" class="mt-1 block w-full rounded border-gray-300 p-2 border text-sm focus:outline-none">
                    <option value="all">Wszyscy użytkownicy systemu</option>
                    <option value="classes">Wybrane oddziały klasowe</option>
                </select>
            </div>

            <div id="classes_selector_wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Zaznacz klasy</label>
                <div class="border rounded p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2 bg-gray-50 text-xs">
                    @foreach($schoolClasses as $class)
                        <label class="flex items-center space-x-2 p-1 hover:bg-white rounded cursor-pointer">
                            <input type="checkbox" name="school_classes[]" value="{{ $class->id }}" class="rounded text-blue-600 border-gray-300">
                            <span>Klasa {{ $class->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Treść ogłoszenia</label>
                <textarea name="content" required rows="5" class="mt-1 block w-full rounded border-gray-300 p-2 border text-sm focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="closeAnnouncementModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded font-semibold text-sm hover:bg-gray-400">Anuluj</button>
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-emerald-700">Publikuj</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAnnouncementModal() {
        document.getElementById('announcement_modal').classList.remove('hidden');
        document.getElementById('announcement_modal').classList.add('flex');
    }

    function closeAnnouncementModal() {
        document.getElementById('announcement_modal').classList.add('hidden');
        document.getElementById('announcement_modal').classList.remove('flex');
    }

    function toggleClassesSelector(val) {
        const wrapper = document.getElementById('classes_selector_wrapper');
        if (val === 'classes') {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }
</script>
@endif
@endsection