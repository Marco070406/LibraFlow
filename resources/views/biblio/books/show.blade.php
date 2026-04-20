<x-app-layout>
    <x-slot name="header">{{ $book->title }}</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Breadcrumb + actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('biblio.books.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Retour au catalogue
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('biblio.books.edit', $book) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 text-sm font-medium rounded-xl hover:bg-amber-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                    Modifier
                </a>
                <form method="POST" action="{{ route('biblio.books.destroy', $book) }}"
                      onsubmit="return confirm('Supprimer « {{ addslashes($book->title) }} » ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-xl hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        {{-- Fiche livre --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-8">
                    {{-- Couverture --}}
                    <div class="flex-shrink-0">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}"
                                 alt="{{ $book->title }}"
                                 class="w-48 h-64 rounded-xl object-cover border border-gray-200 shadow-sm">
                        @else
                            <div class="w-48 h-64 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center border border-gray-200">
                                <svg class="w-16 h-16 text-indigo-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Détails --}}
                    <div class="flex-1 min-w-0 space-y-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ $book->title }}</h2>
                            <p class="text-lg text-gray-500 mt-1">{{ $book->author }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">{{ $book->category }}</span>
                            @if($book->isbn)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 font-mono">ISBN : {{ $book->isbn }}</span>
                            @endif
                        </div>

                        {{-- Disponibilité visuelle --}}
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-600 mb-2">Exemplaires</p>
                            <div class="flex items-center gap-2">
                                @for($i = 0; $i < $book->total_copies; $i++)
                                    @if($i < $book->available_copies)
                                        <svg class="w-8 h-8 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                        </svg>
                                    @else
                                        <svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-xs text-gray-400 mt-2">
                                <span class="font-semibold text-emerald-600">{{ $book->available_copies }}</span> disponible(s) sur {{ $book->total_copies }} total
                            </p>
                        </div>

                        @if($book->description)
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Description</p>
                                <p class="text-sm text-gray-500 leading-relaxed">{{ $book->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Emprunts actifs --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Emprunts actifs</h3>
                    <span class="text-xs font-medium bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full">{{ $book->loans->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($book->loans as $loan)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $loan->user->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">Emprunté le {{ $loan->borrowed_at?->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                @if($loan->isOverdue())
                                    <span class="text-xs font-bold text-red-600">En retard</span>
                                @else
                                    <span class="text-xs text-gray-500">Retour le {{ $loan->due_at?->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="px-6 py-6 text-sm text-center text-gray-400">Aucun emprunt actif.</p>
                    @endforelse
                </div>
            </div>

            {{-- Réservations en attente --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Réservations en attente</h3>
                    <span class="text-xs font-medium bg-purple-100 text-purple-700 px-2.5 py-0.5 rounded-full">{{ $book->reservations->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($book->reservations as $reservation)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $reservation->user->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">Réservé le {{ $reservation->reserved_at?->format('d/m/Y') }}</p>
                            </div>
                            @php
                                $sc = match($reservation->status) {
                                    'en_attente' => 'bg-amber-100 text-amber-700',
                                    'notifie' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $sc }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                        </div>
                    @empty
                        <p class="px-6 py-6 text-sm text-center text-gray-400">Aucune réservation en attente.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
