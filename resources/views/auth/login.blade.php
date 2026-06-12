<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBook - Logowanie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <div class="flex flex-col items-center space-x-3 px-1 py-1 border-slate-700 mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="EduBook Logo" class="h-40 w-auto object-contain">
        </div>
        
        @if(session('info'))
            <div class="bg-blue-100 text-blue-700 p-2 rounded mb-4 text-sm text-center">
                {{ session('info') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Hasło</label>
                <input type="password" name="password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-sm text-gray-600">Zapamiętaj mnie</label>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">Zaloguj się</button>
        </form>
    </div>
</body>
</html>