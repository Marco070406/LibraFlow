{{--
    ═══════════════════════════════════════════════════════
    VUE : Catalogue lecteur — resources/views/lecteur/books/index.blade.php
    ═══════════════════════════════════════════════════════

    DÉCISIONS UX (justifications) :
    ──────────────────────────────
    1. BARRE DE RECHERCHE EN PREMIER
       → C'est l'intention principale du lecteur (trouver un livre précis).
         Elle doit être la plus visible et la plus accessible.

    2. FILTRE CATÉGORIE ENSUITE
       → Aide à affiner la recherche lorsque le lecteur sait ce qu'il veut
         mais pas le titre exact. Placé après la recherche car moins urgent.

    3. TOGGLE "DISPONIBLES SEULEMENT" DÉSACTIVÉ PAR DÉFAUT
       → Masquer des livres sans raison réduit l'exploration et peut décourager
         le lecteur. Le laisser désactivé par défaut permet de voir tout le
         catalogue et d'utiliser la réservation. L'utilisateur active le filtre
         s'il a besoin d'emprunter immédiatement.

    4. GRILLE RESPONSIVE (3 → 2 → 1 colonnes)
       → Desktop : 3 colonnes pour maximiser la densité d'information.
         Tablette : 2 colonnes pour la lisibilité.
         Mobile   : 1 colonne pour éviter les cartes trop compactes.

    5. PAGINATION AVEC QUERY STRING PERSISTANT
       → Les filtres sont conservés dans l'URL, ce qui permet de
         partager un lien de recherche et de revenir à la même page.

    6. MESSAGE D'ÉTAT VIDE BIENVEILLANT
       → Plutôt qu'un simple "aucun résultat", on guide le lecteur
         et on l'invite à solliciter le bibliothécaire.
    ═══════════════════════════════════════════════════════
--}}
<x-app-layout>
    <x-slot name="header">Catalogue</x-slot>

    <div class="space-y-6">

        {{-- Titre --}}
        <div>
            <h2 class="text-xl font-bold text-gray-800">Explorer le catalogue</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $books->total() }} livre{{ $books->total() > 1 ? 's' : '' }} dans la bibliothèque
                @if(array_filter(array_values($filters)))
                    · <span class="text-indigo-600 font-medium">filtres actifs</span>
                @endif
            </p>
        </div>

        {{-- ── Filtres ── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" action="{{ route('lecteur.books.index') }}"
                  class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">

                {{-- Filtre 1 : Recherche (intention principale) --}}
                <div class="flex-1 relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input id="search-input"
                           type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Rechercher un titre, auteur, catégorie…"
                           autocomplete="off"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow">
                </div>

                {{-- Filtre 2 : Catégorie --}}
                <select id="category-select"
                        name="category"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white
                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>

                {{-- Filtre 3 : Disponibilité (désactivé par défaut) --}}
                <label id="available-toggle"
                       class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl
                              text-sm cursor-pointer hover:bg-gray-50 transition-colors select-none shrink-0">
                    <input type="checkbox"
                           name="available_only"
                           value="1"
                           {{ !empty($filters['available_only']) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-gray-600">Disponibles seulement</span>
                </label>

                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl
                               hover:bg-indigo-700 active:scale-95 transition-all shrink-0">
                    Rechercher
                </button>

                @if(array_filter(array_values($filters)))
                    <a href="{{ route('lecteur.books.index') }}"
                       class="px-4 py-2.5 border border-gray-200 text-gray-500 text-sm rounded-xl
                              hover:bg-gray-50 transition-colors shrink-0 text-center">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        {{-- ── Résultats ── --}}
        @if($books->count())
            {{-- Grille : 1 col mobile / 2 col tablette / 3 col desktop --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($books as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>

            @if($books->hasPages())
                <div class="flex justify-center">
                    {{ $books->links() }}
                </div>
            @endif
        @else
            {{-- Message bienveillant si aucun résultat --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-14 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-700 mb-2">Aucun livre ne correspond à votre recherche</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto">
                    Essayez avec d'autres mots-clés ou demandez à votre bibliothécaire d'ajouter ce titre au catalogue.
                </p>
                <a href="{{ route('lecteur.books.index') }}"
                   class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl
                          hover:bg-indigo-700 transition-colors">
                    Voir tous les livres
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
