<x-app-layout>
    <x-slot name="header">Mon espace lecteur</x-slot>

    <div class="space-y-6">

        {{-- ── 1. Carte de bienvenue ── --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-lg p-6 text-white">
            <div class="relative z-10">
                <p class="text-indigo-200 text-sm font-medium">{{ now()->translatedFormat('l d F Y') }}</p>
                <h2 class="text-2xl font-bold mt-1">Bonjour, {{ auth()->user()->name }} </h2>
                @if($stats['active_loans'] > 0)
                    <p class="mt-2 text-indigo-100 text-sm">
                        Vous avez <span class="font-semibold text-white">{{ $stats['active_loans'] }} emprunt{{ $stats['active_loans'] > 1 ? 's' : '' }} en cours</span>.
                        @if($stats['overdue'] > 0)
                            <span class="ml-1 bg-yellow-400/20 text-yellow-200 px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ $stats['overdue'] }} en retard
                            </span>
                        @endif
                    </p>
                @else
                    <p class="mt-2 text-indigo-100 text-sm">Aucun emprunt actif — le catalogue vous attend !</p>
                @endif
            </div>
            <div class="absolute -right-8 -top-8 w-44 h-44 bg-white/10 rounded-full"></div>
            <div class="absolute right-12 -bottom-10 w-32 h-32 bg-white/10 rounded-full"></div>
            <div class="absolute -left-4 -bottom-6 w-24 h-24 bg-white/5 rounded-full"></div>
        </div>

        {{-- ── Stat cards ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunts actifs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-0.5">{{ $stats['active_loans'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Réservations</p>
                    <p class="text-3xl font-bold text-gray-800 mt-0.5">{{ $stats['reservations'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl {{ $stats['overdue'] > 0 ? 'bg-amber-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 {{ $stats['overdue'] > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Retards</p>
                    <p class="text-3xl font-bold {{ $stats['overdue'] > 0 ? 'text-amber-600' : 'text-gray-800' }} mt-0.5">{{ $stats['overdue'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total emprunté</p>
                    <p class="text-3xl font-bold text-gray-800 mt-0.5">{{ $stats['total_borrowed'] }}</p>
                </div>
            </div>
        </div>

        {{-- ── Alerte retard bienveillante (dashboard) ── --}}
        @if($stats['overdue'] > 0)
            <div class="flex items-start gap-4 p-4 rounded-2xl border-l-4" style="background-color:#fefce8;border-color:#ca8a04;">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center" style="background-color:#fef9c3;">
                    <svg class="w-5 h-5" style="color:#a16207;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold" style="color:#78350f;">Un petit rappel 📚</p>
                    <p class="text-sm mt-0.5" style="color:#92400e;">
                        Vous avez {{ $stats['overdue'] }} emprunt{{ $stats['overdue'] > 1 ? 's' : '' }} en retard.
                        Pensez à rapporter {{ $stats['overdue'] > 1 ? 'ces livres' : 'ce livre' }} dès que possible — merci !
                    </p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ── 2. Mes emprunts en cours avec barres de progression ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Mes emprunts en cours</h3>
                    <span class="text-xs font-medium bg-indigo-100 text-indigo-700 px-2.5 py-0.5 rounded-full">{{ $stats['active_loans'] }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($activeLoans as $loan)
                        <div class="px-6 py-4 space-y-2">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $loan->book->title ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $loan->book->author ?? '—' }}</p>
                                </div>
                            </div>
                            {{-- Barre de progression --}}
                            <x-loan-progress-bar :loan="$loan" />
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center">
                            <svg class="w-10 h-10 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <p class="text-sm text-gray-400">Aucun emprunt actif.</p>
                            <a href="{{ route('lecteur.books.index') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:text-indigo-800 font-medium">Explorer le catalogue →</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ── 3. Mes réservations avec statut et position ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Mes réservations</h3>
                    <span class="text-xs text-gray-400">5 dernières</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($reservations as $reservation)
                        @php
                            $position = null;
                            if ($reservation->status === 'en_attente') {
                                $position = \App\Models\Reservation::where('book_id', $reservation->book_id)
                                    ->where('status', 'en_attente')
                                    ->where('reserved_at', '<=', $reservation->reserved_at)
                                    ->count();
                            }
                        @endphp
                        <div class="flex items-center gap-4 px-6 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $reservation->book->title ?? '—' }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $reservation->reserved_at?->format('d/m/Y') }}
                                    @if($position)
                                        · position <strong class="text-amber-600">{{ $position }}</strong> dans la file
                                    @endif
                                </p>
                            </div>
                            <x-reservation-status :reservation="$reservation" />
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center">
                            <p class="text-sm text-gray-400">Aucune réservation.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ── 4. Nouveautés du catalogue ── --}}
        @if($latestBooks->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Nouveautés du catalogue</h3>
                    <a href="{{ route('lecteur.books.index') }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        Voir tout →
                    </a>
                </div>
                <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($latestBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
