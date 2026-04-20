<x-app-layout>
    <x-slot name="header">Tableau de bord — Bibliothécaire</x-slot>

    <div class="space-y-6">

        {{-- Welcome banner --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold">Bienvenue, {{ auth()->user()->name }} </h2>
                <p class="mt-1 text-amber-100 text-sm">Gestion de la bibliothèque — {{ now()->translatedFormat('l d F Y') }}</p>
            </div>
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute -right-4 -bottom-8 w-28 h-28 bg-white/10 rounded-full"></div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Livres au catalogue --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Livres</p>
                    <p class="text-3xl font-bold text-gray-800 mt-0.5">{{ $stats['books'] }}</p>
                </div>
            </div>

            {{-- Emprunts en cours --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h1.5m9 0h-9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunts actifs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-0.5">{{ $stats['active_loans'] }}</p>
                </div>
            </div>

            {{-- Retards --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">En retard</p>
                    <p class="text-3xl font-bold {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-gray-800' }} mt-0.5">{{ $stats['overdue'] }}</p>
                </div>
            </div>

            {{-- Réservations en attente --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Réservations</p>
                    <p class="text-3xl font-bold text-gray-800 mt-0.5">{{ $stats['pending_reservations'] }}</p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Emprunts en retard --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800"> Retards à traiter</h3>
                    @if($stats['overdue'] > 0)
                        <span class="text-xs font-medium bg-red-100 text-red-700 px-2.5 py-0.5 rounded-full">{{ $stats['overdue'] }}</span>
                    @endif
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($overdueLoans as $loan)
                        <div class="flex items-center gap-4 px-6 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $loan->book->title ?? '—' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $loan->user->name ?? '—' }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-bold text-red-600">{{ $loan->due_at?->diffForHumans() }}</p>
                                <p class="text-xs text-gray-400">{{ $loan->due_at?->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="px-6 py-8 text-sm text-center text-gray-400"> Aucun retard en cours.</p>
                    @endforelse
                </div>
            </div>

            {{-- Derniers emprunts --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Derniers emprunts</h3>
                    <span class="text-xs text-gray-400">5 derniers</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentLoans as $loan)
                        <div class="flex items-center gap-4 px-6 py-3">
                            <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-semibold text-sm flex-shrink-0">
                                {{ strtoupper(substr($loan->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $loan->book->title ?? '—' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $loan->user->name ?? '—' }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                @if($loan->returned_at)
                                    <span class="text-xs font-medium bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Rendu</span>
                                @elseif($loan->isOverdue())
                                    <span class="text-xs font-medium bg-red-100 text-red-700 px-2 py-0.5 rounded-full">En retard</span>
                                @else
                                    <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">En cours</span>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ $loan->borrowed_at?->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="px-6 py-8 text-sm text-center text-gray-400">Aucun emprunt enregistré.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
