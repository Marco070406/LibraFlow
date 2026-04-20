<x-app-layout>
    <x-slot name="header">{{ $book->title }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Retour --}}
        <a href="{{ route('lecteur.books.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Retour au catalogue
        </a>

        {{-- Fiche livre --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col md:flex-row gap-8">

                    {{-- Couverture --}}
                    <div class="flex-shrink-0">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}"
                                 alt="{{ $book->title }}"
                                 class="w-52 h-72 rounded-xl object-cover border border-gray-200 shadow-sm mx-auto md:mx-0">
                        @else
                            <div class="w-52 h-72 rounded-xl bg-gradient-to-br from-indigo-50 to-purple-100
                                        flex items-center justify-center border border-gray-200 mx-auto md:mx-0">
                                <svg class="w-20 h-20 text-indigo-200" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Détails --}}
                    <div class="flex-1 min-w-0 space-y-5">

                        <div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if($book->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                        {{ $book->category }}
                                    </span>
                                @endif
                                @if($book->isbn)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 font-mono">
                                        ISBN : {{ $book->isbn }}
                                    </span>
                                @endif
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h2>
                            <p class="text-base text-gray-500 mt-1">par <span class="font-medium text-gray-700">{{ $book->author }}</span></p>
                        </div>

                        {{-- ── Disponibilité visuelle avec icônes SVG inline ── --}}
                        @php
                            $available    = $book->available_copies;
                            $total        = $book->total_copies;
                            $borrowed     = $total - $available;
                            $canBorrow    = $available > 0;
                            $canReserve   = !$canBorrow;

                            // Position estimée dans la file
                            $queueCount = \App\Models\Reservation::where('book_id', $book->id)
                                ->where('status', 'en_attente')
                                ->count();

                            // Réservation existante du lecteur
                            $hasReservation = auth()->check()
                                ? \App\Models\Reservation::where('book_id', $book->id)
                                    ->where('user_id', auth()->id())
                                    ->whereIn('status', ['en_attente', 'notifie'])
                                    ->exists()
                                : false;
                        @endphp

                        <div class="{{ $canBorrow ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }}
                                    rounded-xl p-5 border space-y-3">

                            {{-- Ligne d'icônes — une par exemplaire --}}
                            <div class="flex flex-wrap items-center gap-2">
                                @for($i = 0; $i < $total; $i++)
                                    @if($i < $available)
                                        {{-- Exemplaire disponible — vert #16a34a --}}
                                        <div class="w-9 h-11 rounded-md flex items-center justify-center shadow-sm"
                                             style="background-color:#16a34a;" title="Exemplaire disponible">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                            </svg>
                                        </div>
                                    @else
                                        {{-- Exemplaire emprunté — gris #9ca3af --}}
                                        <div class="w-9 h-11 rounded-md flex items-center justify-center"
                                             style="background-color:#9ca3af;" title="Exemplaire emprunté">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                            </svg>
                                        </div>
                                    @endif
                                @endfor
                            </div>

                            {{-- Texte récapitulatif --}}
                            <p class="text-sm {{ $canBorrow ? 'text-emerald-700' : 'text-amber-700' }}">
                                <span class="font-semibold">{{ $available }} exemplaire{{ $available > 1 ? 's' : '' }} disponible{{ $available > 1 ? 's' : '' }}</span>
                                sur {{ $total }} au total
                            </p>
                        </div>

                        {{-- ── Boutons d'action ── --}}
                        <div class="flex flex-wrap gap-3">
                            @if($canBorrow)
                                {{-- Bouton "Emprunter" — vert, prominent --}}
                                <div class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                            bg-emerald-600 text-white font-semibold shadow-sm
                                            shadow-emerald-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Disponible — venez l'emprunter à la bibliothèque
                                </div>
                            @endif

                            @if($canReserve)
                                @if($hasReservation)
                                    {{-- Déjà en file --}}
                                    <a href="{{ route('lecteur.reservations.index') }}"
                                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl
                                              bg-orange-50 border border-orange-200 text-orange-700 font-semibold
                                              hover:bg-orange-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Déjà en file d'attente — voir mes réservations
                                    </a>
                                @else
                                    {{-- Rejoindre la file — orange avec position estimée --}}
                                    <form method="POST" action="{{ route('lecteur.reservations.store', $book) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                                                       bg-orange-500 text-white font-semibold shadow-sm
                                                       hover:bg-orange-600 active:scale-95 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Rejoindre la file d'attente
                                            @if($queueCount > 0)
                                                <span class="ml-1 bg-white/20 px-2 py-0.5 rounded-full text-xs font-bold">
                                                    position {{ $queueCount + 1 }}
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($book->description)
                            <div class="pt-4 border-t border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">À propos de ce livre</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">{{ $book->description }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
