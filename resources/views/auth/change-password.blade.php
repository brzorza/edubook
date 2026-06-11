<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wymagana zmiana hasła</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-xl font-bold mb-4 text-center text-gray-800">Pierwsze logowanie</h2>
        <p class="text-gray-600 text-sm mb-6 text-center">Ze względów bezpieczeństwa musisz zmienić swoje hasło początkowe.</p>

        <form action="{{ route('password.change.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Obecne hasło</label>
                <input type="password" name="current_password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nowe hasło (min. 8 znaków)</label>
                <input type="password" name="password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Powtórz nowe hasło</label>
                <input type="password" name="password_confirmation" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full bg-green-600 text-white p-2 rounded font-bold hover:bg-green-700 transition">Zapisz nowe hasło</button>
        </form>
    </div>
</body>
</html>