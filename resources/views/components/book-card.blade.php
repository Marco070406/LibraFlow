{{--
    Composant : book-card
    Paramètres :
      - $book  : App\Models\Book
      - $route : nom de la route pour le lien (défaut: 'lecteur.books.show')
--}}
@props(['book', 'route' => 'lecteur.books.show'])

@php
    $status = \App\Helpers\BookHelper::getAvailabilityStatus($book);
    $hasReservation = auth()->check() && \App\Models\Reservation::where('book_id', $book->id)
        ->where('user_id', auth()->id())
        ->whereIn('status', ['en_attente', 'notifie'])
        ->exists();
@endphp

<div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden
          hover:shadow-md hover:border-indigo-200 hover:-translate-y-0.5
          transition-all duration-200 flex flex-col">

    <a href="{{ route($route, $book) }}" class="flex-1 flex flex-col">
        {{-- Couverture --}}
        <div class="aspect-[3/4] bg-gradient-to-br from-gray-100 to-gray-50 relative overflow-hidden flex-shrink-0">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}"
                     alt="{{ $book->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
            @endif

            {{-- Badge catégorie --}}
            @if($book->category)
                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-semibold
                             bg-white/90 text-gray-700 backdrop-blur-sm shadow-sm">
                    {{ $book->category }}
                </span>
            @endif

            {{-- Badge disponibilité --}}
            <div class="absolute bottom-2 right-2">
                <x-availability-badge :book="$book" size="sm" />
            </div>
        </div>

        {{-- Infos --}}
        <div class="p-4 flex-1 flex flex-col gap-1">
            <h3 class="text-sm font-bold text-gray-800 line-clamp-2
                       group-hover:text-indigo-600 transition-colors leading-tight">
                {{ $book->title }}
            </h3>
            <p class="text-xs text-gray-400 truncate">{{ $book->author }}</p>
        </div>
    </a>

    {{-- Actions rapides --}}
    <div class="px-4 pb-4 space-y-2">
        <form method="POST" action="{{ route('lecteur.loans.store', $book) }}">
            @csrf
            <button type="submit"
                    class="w-full text-center px-3 py-2 rounded-xl text-xs font-semibold
                           bg-emerald-600 text-white hover:bg-emerald-700 active:scale-95 transition-all">
                Emprunter
            </button>
        </form>

        @if($hasReservation)
            <a href="{{ route('lecteur.reservations.index') }}"
               class="block w-full text-center px-3 py-2 rounded-xl text-xs font-semibold
                      bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200 transition-all">
                Déjà réservé
            </a>
        @else
            <form method="POST" action="{{ route('lecteur.reservations.store', $book) }}">
                @csrf
                <button type="submit"
                        class="w-full text-center px-3 py-2 rounded-xl text-xs font-semibold
                               bg-orange-500 text-white hover:bg-orange-600 active:scale-95 transition-all">
                    Réserver
                </button>
            </form>
        @endif
    </div>
</div>
