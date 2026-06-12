@extends('layouts.app')

@section('title', 'Zarządzanie Użytkownikami - EduBook')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Zarządzanie Użytkownikami</h1>
        <p class="text-sm text-gray-500 mt-1">Tworzenie kont nauczycieli, rodziców oraz uczniów wraz z automatycznym generowaniem danych do logowania.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 shadow-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('user_created_success'))
        <div class="bg-blue-50 border border-blue-200 text-blue-900 p-4 md:p-5 rounded-lg mb-6 shadow-sm">
            <h3 class="font-bold text-base md:text-lg mb-2 text-blue-800"><i class="fa-solid fa-key mr-2"></i>Konto zostało wygenerowane!</h3>
            <p class="text-xs md:text-sm mb-3">Przekaż poniższe dane użytkownikowi do pierwszego logowania. Hasło musi zostać zmienione po zalogowaniu.</p>
            <div class="bg-white p-3 rounded border font-mono text-xs md:text-sm space-y-1 block sm:inline-block overflow-x-auto">
                <div><strong>Imię i nazwisko:</strong> {{ session('user_created_success')['name'] }}</div>
                <div><strong>Login (E-mail):</strong> {{ session('user_created_success')['email'] }}</div>
                <div><strong>Hasło tymczasowe:</strong> <span class="bg-yellow-100 px-1 font-bold">{{ session('user_created_success')['password'] }}</span></div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200 h-fit">
            <h2 class="text-lg md:text-xl font-bold mb-4 text-gray-700 border-b pb-2">Dodaj Użytkownika</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Imię</label>
                    <input type="text" name="imie" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nazwisko</label>
                    <input type="text" name="nazwisko" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adres E-mail (Login)</label>
                    <input type="email" name="email" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rola w systemie</label>
                    <select name="rola" id="role_select" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="handleRoleChange(this.value)">
                        <option value="nauczyciel">Nauczyciel</option>
                        <option value="rodzic">Rodzic</option>
                        <option value="uczen">Uczeń</option>
                    </select>
                </div>
                <div id="create_class_wrapper" class="hidden">
                    <label class="block text-sm font-medium text-gray-700">Klasa (Opcjonalnie)</label>
                    <select name="school_class_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
                        <option value="">-- Brak przypisania --</option>
                        @foreach($schoolClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="parent-select-container" class="hidden">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Przypisz rodzica (opcjonalnie)</label>
                    <select name="parent_id" id="parent_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
                        <option value="">-- Brak rodzica --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->nazwisko }} {{ $parent->imie }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">Generuj konto</button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b pb-4 mb-4 gap-2">
                <h2 class="text-lg md:text-xl font-bold text-gray-700">Lista Użytkowników</h2>
                <form action="{{ route('admin.users.index') }}" method="GET" class="w-full sm:w-auto">
                    <select name="role" class="w-full sm:w-auto rounded border border-gray-300 p-1.5 text-sm focus:outline-none" onchange="this.form.submit()">
                        <option value="">Wszyscy (oprócz Adminów)</option>
                        <option value="nauczyciel" {{ $selectedRole === 'nauczyciel' ? 'selected' : '' }}>Nauczyciele</option>
                        <option value="rodzic" {{ $selectedRole === 'rodzic' ? 'selected' : '' }}>Rodzice</option>
                        <option value="uczen" {{ $selectedRole === 'uczen' ? 'selected' : '' }}>Uczniowie</option>
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto -mx-4 md:mx-0">
                <div class="inline-block min-w-full align-middle p-4 md:p-0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-xs font-bold border-b border-gray-200 uppercase tracking-wider">
                                <th class="p-3">Użytkownik</th>
                                <th class="p-3">E-mail</th>
                                <th class="p-3">Rola</th>
                                <th class="p-3">Szczegóły</th>
                                <th class="p-3 text-right">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            @forelse($users as $user)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                    <td class="p-3 font-semibold text-gray-800 whitespace-nowrap">{{ $user->nazwisko }} {{ $user->imie }}</td>
                                    <td class="p-3 text-gray-500 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                            {{ $user->rola === 'nauczyciel' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $user->rola === 'rodzic' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $user->rola === 'uczen' ? 'bg-green-100 text-green-800' : '' }}
                                        ">
                                            {{ $user->rola }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-gray-500 whitespace-nowrap">
                                        @if($user->rola === 'uczen')
                                            @if($user->schoolClass)
                                                Klasa: <span class="font-bold text-gray-700">{{ $user->schoolClass->name }}</span>
                                            @endif
                                            @if($user->parent)
                                                <br><span class="text-xs text-gray-400">Rodzic: {{ $user->parent->nazwisko }} {{ $user->parent->imie }}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-3 text-right space-x-1 whitespace-nowrap">
                                        <button onclick="openEditModal('{{ $user->id }}', '{{ $user->imie }}', '{{ $user->nazwisko }}', '{{ $user->email }}', '{{ $user->school_class_id }}', '{{ $user->rola }}', '{{ $user->parent_id }}')" class="text-blue-600 hover:text-blue-900"><i class="fa-solid fa-pen-to-square p-1"></i></button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz bezpowrotnie usunąć tego użytkownika?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"><i class="fa-solid fa-trash p-1"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-400">Brak zarejestrowanych użytkowników dla wybranego kryterium.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="edit_modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden justify-center items-center p-4 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Edycja Użytkownika</h3>
        <form id="edit_form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">Imię</label>
                <input type="text" id="edit_imie" name="imie" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nazwisko</label>
                <input type="text" id="edit_nazwisko" name="nazwisko" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Adres E-mail</label>
                <input type="email" id="edit_email" name="email" required class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
            </div>
            <div id="edit_class_wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700">Klasa</label>
                <select id="edit_school_class_id" name="school_class_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
                    <option value="">-- Brak przypisania --</option>
                    @foreach($schoolClasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="edit_parent_wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700">Przypisz rodzica</label>
                <select id="edit_parent_id" name="parent_id" class="mt-1 block w-full rounded border-gray-300 p-2 border focus:outline-none">
                    <option value="">-- Brak rodzica --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->nazwisko }} {{ $parent->imie }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="closeEditModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded font-semibold hover:bg-gray-400">Anuluj</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-semibold hover:bg-blue-700">Zapisz zmiany</button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleRoleChange(roleValue) {
        const classWrapper = document.getElementById('create_class_wrapper');
        const parentWrapper = document.getElementById('parent-select-container');
        
        if (roleValue === 'uczen') {
            classWrapper.classList.remove('hidden');
            parentWrapper.classList.remove('hidden');
        } else {
            classWrapper.classList.add('hidden');
            parentWrapper.classList.add('hidden');
            document.getElementById('parent_id').value = '';
        }
    }

    function openEditModal(id, firstName, lastName, email, classId, role, parentId) {
        document.getElementById('edit_form').action = '/admin/users/' + id;
        document.getElementById('edit_imie').value = firstName;
        document.getElementById('edit_nazwisko').value = lastName;
        document.getElementById('edit_email').value = email;
        
        const classWrapper = document.getElementById('edit_class_wrapper');
        const parentWrapper = document.getElementById('edit_parent_wrapper');
        
        if (role === 'uczen') {
            classWrapper.classList.remove('hidden');
            parentWrapper.classList.remove('hidden');
            document.getElementById('edit_school_class_id').value = classId ? classId : '';
            document.getElementById('edit_parent_id').value = parentId ? parentId : '';
        } else {
            classWrapper.classList.add('hidden');
            parentWrapper.classList.add('hidden');
            document.getElementById('edit_school_class_id').value = '';
            document.getElementById('edit_parent_id').value = '';
        }

        document.getElementById('edit_modal').classList.remove('hidden');
        document.getElementById('edit_modal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('edit_modal').classList.add('hidden');
        document.getElementById('edit_modal').classList.remove('flex');
    }
</script>
@endsection