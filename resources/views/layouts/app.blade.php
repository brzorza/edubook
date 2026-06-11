<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EduBook')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <div class="flex h-screen overflow-hidden relative">
        
        <div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-800 text-white flex flex-col justify-between shadow-xl transform -translate-x-full lg:translate-x-0 lg:static lg:inset-auto transition-transform duration-300 ease-in-out">
            <div>
                <div class="p-5 text-2xl font-bold tracking-wider text-center border-b border-slate-700 bg-slate-900 flex justify-between items-center lg:justify-center">
                    <span>EduBook</span>
                    <button onclick="toggleSidebar()" class="text-white lg:hidden focus:outline-none">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <div class="p-4 border-b border-slate-700 bg-slate-850 flex items-center space-x-3">
                    <div class="w-10 h-10 bg-slate-600 rounded-full flex items-center justify-center text-lg font-bold">
                        {{ strtoupper(substr(Auth::user()->imie, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold truncate">{{ Auth::user()->imie }} {{ Auth::user()->nazwisko }}</p>
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-bold">{{ Auth::user()->rola }}</span>
                    </div>
                </div>

                <nav class="p-4 space-y-2">
                    @if(Auth::user()->rola === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('admin/dashboard') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-sitemap w-5 text-center"></i>
                            <span>Struktura Szkoły</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('admin/users*') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-users w-5 text-center"></i>
                            <span>Użytkownicy</span>
                        </a>
                    @endif

                    @if(Auth::user()->rola === 'nauczyciel')
                        <a href="{{ route('teacher.journal.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('teacher/journal*') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-book-open w-5 text-center"></i>
                            <span>Dziennik Klasowy</span>
                        </a>
                    @endif

                    @if(in_array(Auth::user()->rola, ['uczen', 'rodzic']))
                        <a href="{{ route('student.grades') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('student/grades') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-graduation-cap w-5 text-center"></i>
                            <span>Oceny</span>
                        </a>
                        <a href="{{ route('student.attendance') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('student/attendance') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                            <span>Frekwencja</span>
                        </a>
                        <a href="{{ route('student.timetable') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('student/timetable') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-calendar-week w-5 text-center"></i>
                            <span>Plan Lekcji</span>
                        </a>
                    @endif

                    <a href="{{ route('announcements.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('announcements*') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                        <i class="fa-solid fa-bullhorn w-5 text-center"></i>
                        <span>Ogłoszenia</span>
                    </a>

                    <a href="{{ route('messages.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('messages*') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                        <i class="fa-solid fa-envelope w-5 text-center"></i>
                        <span>Wiadomości</span>
                    </a>

                    @if(in_array(Auth::user()->rola, ['admin', 'nauczyciel', 'dyrekcja']))
                        <a href="{{ route('analytics.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('analytics') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-chart-line w-5 text-center"></i>
                            <span>Statystyki i Raporty</span>
                        </a>
                    @endif

                    @if(Auth::user()->rola === 'admin')
                        <a href="{{ route('analytics.logs') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700 transition {{ Request::is('analytics/logs') ? 'bg-blue-600 hover:bg-blue-600' : '' }}">
                            <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                            <span>Logi Systemowe</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="p-4 border-t border-slate-700 bg-slate-900">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-red-600 hover:bg-red-700 text-white p-2.5 rounded-lg font-semibold transition shadow-md">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Wyloguj się</span>
                    </button>
                </form>
            </div>
        </div>

        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black opacity-50 z-30 hidden lg:hidden"></div>

        <div class="flex-1 flex flex-col overflow-y-auto">
            
            <header class="bg-white shadow-sm p-4 flex justify-between items-center border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <button onclick="toggleSidebar()" class="text-gray-700 lg:hidden focus:outline-none text-xl">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="text-sm text-gray-500 font-medium hidden sm:block">
                        System Dziennika Elektronicznego
                    </div>
                </div>
                <div class="text-sm font-semibold text-gray-700 bg-gray-100 px-3 py-1.5 rounded-md">
                    {{ date('d.m.Y') }}
                </div>
            </header>

            <main class="p-4 md:p-8 flex-1">
                @yield('content')
            </main>
            
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>