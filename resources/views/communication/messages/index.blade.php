@extends('layouts.app')

@section('title', 'Wiadomości - EduBook')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Skrzynka Wiadomości</h1>
            <p class="text-sm text-gray-500 mt-1">Wewnętrzna komunikacja systemu szkolnego.</p>
        </div>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button onclick="openNewMessageModal(false)" class="flex-1 sm:flex-initial bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded font-bold transition text-sm whitespace-nowrap">
                <i class="fa-solid fa-paper-plane mr-1.5"></i> Napisz wiadomość
            </button>
            <button onclick="openNewMessageModal(true)" class="flex-1 sm:flex-initial bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded font-bold transition text-sm whitespace-nowrap">
                <i class="fa-solid fa-users mr-1.5"></i> Wyślij do klasy
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-200 shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="border-b pb-4 mb-4">
                <h2 class="text-lg font-bold text-gray-700 mb-3"><i class="fa-solid fa-inbox mr-2 text-blue-500"></i>Odebrane</h2>
                <form action="{{ route('messages.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Szukaj po nazwisku nadawcy..." class="w-full pl-9 pr-4 py-2 text-sm border rounded border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                </form>
            </div>

            <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                @forelse($receivedMessages as $msg)
                    <div onclick="readMessage('{{ $msg->id }}', '{{ $msg->sender->imie }} {{ $msg->sender->nazwisko }}', '{{ $msg->title }}', `{{ $msg->content }}`, '{{ $msg->created_at->format('d.m.Y H:i') }}')" 
                         class="p-3 border rounded-lg cursor-pointer transition flex flex-col justify-between gap-1 {{ !$msg->read_at ? 'bg-blue-50/70 border-blue-200 hover:bg-blue-50' : 'bg-gray-50 border-gray-100 hover:bg-gray-100' }}">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm text-gray-800">{{ $msg->sender->nazwisko }} {{ $msg->sender->imie }}</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $msg->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-700 truncate">{{ $msg->title }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ $msg->content }}</div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">Brak wiadomości odebranych.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="border-b pb-4 mb-4 h-[73px] flex items-center">
                <h2 class="text-lg font-bold text-gray-700"><i class="fa-solid fa-share-from-square mr-2 text-indigo-500"></i>Wysłane</h2>
            </div>

            <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                @forelse($sentMessages as $msg)
                    <div onclick="viewSentMessage('{{ $msg->recipient->imie }} {{ $msg->recipient->nazwisko }}', '{{ $msg->title }}', `{{ $msg->content }}`, '{{ $msg->created_at->format('d.m.Y H:i') }}')" 
                         class="p-3 bg-gray-50 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-100 transition flex flex-col justify-between gap-1">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-sm text-gray-600">Do: {{ $msg->recipient->nazwisko }} {{ $msg->recipient->imie }}</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $msg->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-700 truncate">{{ $msg->title }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ $msg->content }}</div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">Brak wiadomości wysłanych.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div id="message_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="bg-slate-800 text-white p-4 flex justify-between items-center">
            <h3 id="modal_msg_title" class="font-bold truncate pr-4 text-base"></h3>
            <button onclick="closeMessageModal()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="p-5 space-y-4 flex-1">
            <div class="flex justify-between items-center text-xs border-b pb-2 text-gray-500">
                <div><span id="modal_msg_role_label"></span>: <span id="modal_msg_user" class="font-bold text-gray-700"></span></div>
                <div id="modal_msg_date" class="font-mono"></div>
            </div>
            <div id="modal_msg_content" class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded border border-gray-100 min-h-[120px]"></div>
        </div>
        <div class="bg-gray-100 px-4 py-3 flex justify-end">
            <button onclick="closeMessageModal()" class="bg-gray-600 text-white text-sm font-bold px-4 py-2 rounded hover:bg-gray-700">Zamknij</button>
        </div>
    </div>
</div>

<div id="compose_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
        <div class="bg-slate-800 text-white p-4 flex justify-between items-center">
            <h3 id="compose_title" class="font-bold text-base">Nowa Wiadomość</h3>
            <button type="button" onclick="closeComposeModal()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('messages.store') }}" method="POST" class="p-4 space-y-4">
            @csrf
            
            <div id="recipient_single_wrapper">
                <label class="block text-sm font-medium text-gray-700">Odbiorca (Wpisz nazwisko)</label>
                <input type="text" id="user_search_input" autocomplete="off" placeholder="Zacznij pisać nazwisko..." class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none text-sm">
                <input type="hidden" name="recipient_id" id="recipient_id_hidden">
                <div id="search_results" class="bg-white border rounded mt-1 hidden max-h-40 overflow-y-auto shadow-lg text-sm text-gray-700 z-50 relative divide-y divide-gray-100"></div>
            </div>

            <div id="recipient_class_wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700">Wybierz Klasę Docelową</label>
                <select name="school_class_id" id="school_class_id_field" class="mt-1 block w-full rounded border-gray-300 p-2 border text-sm focus:outline-none">
                    @foreach($schoolClasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tytuł</label>
                <input type="text" name="title" required class="mt-1 block w-full rounded border-gray-300 p-2 border text-sm focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Treść</label>
                <textarea name="content" required rows="5" class="mt-1 block w-full rounded border-gray-300 p-2 border text-sm focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="closeComposeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded font-semibold text-sm hover:bg-gray-400">Anuluj</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-blue-700">Wyślij</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openNewMessageModal(isMass) {
        const singleWrapper = document.getElementById('recipient_single_wrapper');
        const classWrapper = document.getElementById('recipient_class_wrapper');
        const classField = document.getElementById('school_class_id_field');
        const searchInput = document.getElementById('user_search_input');

        if (isMass) {
            document.getElementById('compose_title').innerText = "Masowa wiadomość do klasy";
            singleWrapper.classList.add('hidden');
            classWrapper.classList.remove('hidden');
            classField.required = true;
            searchInput.required = false;
        } else {
            document.getElementById('compose_title').innerText = "Nowa Wiadomość";
            singleWrapper.classList.remove('hidden');
            classWrapper.classList.add('hidden');
            classField.required = false;
            classField.value = "";
            searchInput.required = true;
        }
        document.getElementById('compose_modal').classList.remove('hidden');
        document.getElementById('compose_modal').classList.add('flex');
    }

    function closeComposeModal() {
        document.getElementById('compose_modal').classList.add('hidden');
        document.getElementById('compose_modal').classList.remove('flex');
    }

    function readMessage(id, user, title, content, date) {
        document.getElementById('modal_msg_role_label').innerText = "Nadawca";
        document.getElementById('modal_msg_user').innerText = user;
        document.getElementById('modal_msg_title').innerText = title;
        document.getElementById('modal_msg_content').innerText = content;
        document.getElementById('modal_msg_date').innerText = date;

        fetch(`/messages/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        });

        document.getElementById('message_modal').classList.remove('hidden');
        document.getElementById('message_modal').classList.add('flex');
    }

    function viewSentMessage(user, title, content, date) {
        document.getElementById('modal_msg_role_label').innerText = "Odbiorca";
        document.getElementById('modal_msg_user').innerText = user;
        document.getElementById('modal_msg_title').innerText = title;
        document.getElementById('modal_msg_content').innerText = content;
        document.getElementById('modal_msg_date').innerText = date;

        document.getElementById('message_modal').classList.remove('hidden');
        document.getElementById('message_modal').classList.add('flex');
    }

    function closeMessageModal() {
        document.getElementById('message_modal').classList.add('hidden');
        document.getElementById('message_modal').classList.remove('flex');
    }

    document.getElementById('user_search_input').addEventListener('input', function() {
        const query = this.value;
        const resultsContainer = document.getElementById('search_results');
        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }
        fetch(`/messages/search-users?q=${query}`)
            .then(res => res.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if (data.length === 0) {
                    resultsContainer.innerHTML = '<div class="p-2 text-gray-400 text-xs">Brak wyników</div>';
                } else {
                    data.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'p-2.5 hover:bg-gray-100 cursor-pointer text-xs font-medium';
                        div.innerText = `${user.nazwisko} ${user.imie} (${user.rola})`;
                        div.onclick = () => {
                            document.getElementById('user_search_input').value = `${user.nazwisko} ${user.imie}`;
                            document.getElementById('recipient_id_hidden').value = user.id;
                            resultsContainer.classList.add('hidden');
                        };
                        resultsContainer.appendChild(div);
                    });
                }
                resultsContainer.classList.remove('hidden');
            });
    });
</script>
@endsection