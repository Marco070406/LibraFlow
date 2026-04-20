<x-app-layout>
    <x-slot name="header">Nouvel emprunt</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        <div>
            <h2 class="text-xl font-bold text-gray-800">Enregistrer un emprunt</h2>
            <p class="text-sm text-gray-400 mt-0.5">Retour prévu : <span class="font-medium text-indigo-600">{{ $dueDateFormatted }}</span></p>
        </div>

        <form method="POST" action="{{ route('biblio.loans.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf

            {{-- Recherche lecteur --}}
            <div x-data="userSearch()" class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Lecteur <span class="text-red-500">*</span></label>

                {{-- Champ de recherche --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="search"
                           @focus="if(query.length >= 2) open = true"
                           @keydown.escape="open = false"
                           placeholder="Rechercher par nom ou email..."
                           autocomplete="off"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">

                    {{-- Dropdown résultats --}}
                    <div x-show="open && results.length > 0"
                         x-transition
                         @click.outside="open = false"
                         class="absolute top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden"
                         x-cloak>
                        <template x-for="user in results" :key="user.id">
                            <button type="button"
                                    @click="select(user)"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 text-left transition-colors">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm flex-shrink-0"
                                     x-text="user.name.charAt(0).toUpperCase()"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="user.name"></p>
                                    <p class="text-xs text-gray-400" x-text="user.email"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Lecteur sélectionné --}}
                <div x-show="selected" class="flex items-center gap-2 p-3 bg-indigo-50 border border-indigo-200 rounded-lg" x-cloak>
                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-indigo-700" x-text="selected ? selected.name + ' — ' + selected.email : ''"></span>
                    <button type="button" @click="clear" class="ml-auto text-indigo-400 hover:text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Champ caché --}}
                <input type="hidden" name="user_id" x-bind:value="selected ? selected.id : ''">
                @error('user_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Recherche livre --}}
            <div x-data="bookSearch()" class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Livre <span class="text-red-500">*</span></label>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="search"
                           @focus="if(query.length >= 2) open = true"
                           @keydown.escape="open = false"
                           placeholder="Rechercher par titre ou auteur..."
                           autocomplete="off"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">

                    <div x-show="open && results.length > 0"
                         x-transition
                         @click.outside="open = false"
                         class="absolute top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden"
                         x-cloak>
                        <template x-for="book in results" :key="book.id">
                            <button type="button"
                                    @click="select(book)"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-indigo-50 text-left transition-colors">
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="book.title"></p>
                                    <p class="text-xs text-gray-400" x-text="book.author"></p>
                                </div>
                                <span class="flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-full"
                                      :class="book.available_copies > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                                      x-text="book.available_copies + ' dispo.'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Livre sélectionné --}}
                <div x-show="selected" class="flex items-center gap-2 p-3 bg-indigo-50 border border-indigo-200 rounded-lg" x-cloak>
                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-indigo-700" x-text="selected ? selected.title + ' — ' + selected.author : ''"></span>
                    <button type="button" @click="clear" class="ml-auto text-indigo-400 hover:text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <input type="hidden" name="book_id" x-bind:value="selected ? selected.id : ''">
                @error('book_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Récapitulatif date --}}
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Date de retour attendue</p>
                    <p class="text-sm text-amber-700 mt-0.5">{{ $dueDateFormatted }}</p>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('biblio.loans.index') }}"
                   class="flex-1 text-center px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:scale-95 transition-all shadow-sm">
                    Enregistrer l'emprunt
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function userSearch() {
            return {
                query: '',
                results: [],
                selected: null,
                open: false,
                async search() {
                    if (this.query.length < 2) { this.results = []; this.open = false; return; }
                    const res = await fetch(`/api/users/search?q=${encodeURIComponent(this.query)}`);
                    this.results = await res.json();
                    this.open = this.results.length > 0;
                },
                select(user) { this.selected = user; this.query = user.name; this.open = false; },
                clear() { this.selected = null; this.query = ''; this.results = []; }
            };
        }

        function bookSearch() {
            return {
                query: '',
                results: [],
                selected: null,
                open: false,
                async search() {
                    if (this.query.length < 2) { this.results = []; this.open = false; return; }
                    const res = await fetch(`/api/books/search?q=${encodeURIComponent(this.query)}`);
                    this.results = await res.json();
                    this.open = this.results.length > 0;
                },
                select(book) { this.selected = book; this.query = book.title; this.open = false; },
                clear() { this.selected = null; this.query = ''; this.results = []; }
            };
        }
    </script>
    @endpush
</x-app-layout>
