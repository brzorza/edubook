@extends('layouts.app')

@section('title', 'Logi Systemowe - EduBook')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dziennik Zdarzeń Systemu</h1>
            <p class="text-sm text-gray-500 mt-1">Śledzenie działań użytkowników oraz operacji na bazach danych (Audyt bezpieczeństwa).</p>
        </div>
        <form action="{{ route('analytics.logs') }}" method="GET" class="w-full md:w-auto relative">
            <input type="text" name="search" value="{{ $search }}" placeholder="Filtruj akcję, opis lub nazwisko..." class="w-full md:w-72 pl-9 pr-4 py-2 border rounded border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-slate-200 text-xs font-bold uppercase tracking-wider">
                        <th class="p-3.5 whitespace-nowrap">Data i czas</th>
                        <th class="p-3.5">Użytkownik</th>
                        <th class="p-3.5">Operacja</th>
                        <th class="p-3.5">Opis zdarzenia</th>
                        <th class="p-3.5 text-right font-mono">IP</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-xs divide-y divide-gray-100 font-medium">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5 font-mono text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="p-3.5 font-semibold text-gray-800 whitespace-nowrap">
                                @if($log->user)
                                    {{ $log->user->nazwisko }} {{ $log->user->imie }} 
                                    <span class="text-[10px] text-gray-400 font-normal">({{ $log->user->rola }})</span>
                                @else
                                    <span class="text-gray-400 italic">Gość / System</span>
                                @endif
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wide
                                    {{ str_contains($log->action, 'UTWORZENIE') ? 'bg-green-100 text-green-800' : '' }}
                                    {{ str_contains($log->action, 'EDYCJA') ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ str_contains($log->action, 'USUNIĘCIE') ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="p-3.5 text-gray-700 min-w-[250px] max-w-sm break-words">{{ $log->description }}</td>
                            <td class="p-3.5 text-right font-mono text-gray-400 whitespace-nowrap">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">Brak zarejestrowanych logów spełniających kryteria wyszukiwania.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->appends(['search' => $search])->links() }}
    </div>
</div>
@endsection