<x-app-layout>
    <x-slot name="header">Gestion des réservations</x-slot>

    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Files d'attente — Réservations</h2>
                <p class="text-sm text-gray-400 mt-0.5">Réservations actives groupées par livre (ordre FIFO)</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg text-xs font-medium text-amber-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $reservations->sum(fn($group) => $group->count()) }} réservation(s) active(s)
            </div>
        </div>

        @if($reservations->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-emerald-600">Aucune réservation active 🎉</p>
                <p class="text-xs mt-1">Toutes les files d'attente sont vides.</p>
            </div>
        @else
            {{-- Un bloc par livre --}}
            @foreach($reservations as $bookId => $group)
                @php $book = $group->first()->book; @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                    {{-- En-tête du livre --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 truncate">{{ $book->title ?? '—' }}</h3>
                            <p class="text-xs text-gray-400">{{ $book->author ?? '' }} — {{ $group->count() }} personne(s) en attente</p>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold text-sm">
                            {{ $group->count() }}
                        </span>
                    </div>

                    {{-- File d'attente numérotée --}}
                    <div class="divide-y divide-gray-50">
                        @foreach($group as $index => $reservation)
                            <div class="flex items-center gap-4 px-6 py-3">

                                {{-- Numéro de position --}}
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                    {{ $index === 0 ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Avatar --}}
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($reservation->user->name ?? 'U', 0, 1)) }}
                                </div>

                                {{-- Infos lecteur --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $reservation->user->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $reservation->user->email ?? '' }}</p>
                                </div>

                                {{-- Date réservation --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs text-gray-500">{{ $reservation->reserved_at?->format('d/m/Y') ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $reservation->reserved_at?->diffForHumans() }}</p>
                                </div>

                                {{-- Statut --}}
                                <div class="flex-shrink-0">
                                    @if($reservation->status === 'notifie')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Notifié
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                            En attente
                                        </span>
                                    @endif
                                </div>

                                {{-- Action annulation --}}
                                <div class="flex-shrink-0">
                                    <form method="POST" action="{{ route('biblio.reservations.cancel', $reservation) }}"
                                          onsubmit="return confirm('Annuler cette réservation ?')">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-red-600 hover:border-red-200 active:scale-95 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Annuler
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-app-layout>
