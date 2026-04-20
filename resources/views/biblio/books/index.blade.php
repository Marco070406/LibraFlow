<x-app-layout>
    <x-slot name="header">Catalogue des livres</x-slot>

    <div class="space-y-6">

        {{-- Header + bouton ajouter --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Gestion du catalogue</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $books->total() }} livre(s) au catalogue</p>
            </div>
            <a href="{{ route('biblio.books.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Ajouter un livre
            </a>
        </div>

        {{-- Filtres --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <form method="GET" action="{{ route('biblio.books.index') }}" class="flex flex-col sm:flex-row gap-3">
                {{-- Recherche --}}
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Rechercher par titre ou auteur…"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                {{-- Catégorie --}}
                <select name="category"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-5 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-xl hover:bg-gray-900 transition-colors">
                    Filtrer
                </button>
                @if(!empty($filters['search']) || !empty($filters['category']))
                    <a href="{{ route('biblio.books.index') }}"
                       class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-center">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Livre</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">ISBN</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Exemplaires</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Disponibilité</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($books as $book)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                {{-- Couverture + Titre --}}
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}"
                                                 alt="{{ $book->title }}"
                                                 class="w-10 h-14 rounded-lg object-cover border border-gray-200 flex-shrink-0">
                                        @else
                                            <div class="w-10 h-14 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 border border-gray-200">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <a href="{{ route('biblio.books.show', $book) }}" class="text-sm font-semibold text-gray-800 hover:text-indigo-600 truncate block">{{ $book->title }}</a>
                                            <p class="text-xs text-gray-400 truncate">{{ $book->author }}</p>
                                        </div>
                                    </div>
                                </td>
                                {{-- Catégorie --}}
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">{{ $book->category }}</span>
                                </td>
                                {{-- ISBN --}}
                                <td class="px-6 py-3 text-sm text-gray-500 font-mono">{{ $book->isbn ?: '—' }}</td>
                                {{-- Exemplaires --}}
                                <td class="px-6 py-3 text-center">
                                    <span class="text-sm font-semibold text-gray-800">{{ $book->available_copies }}</span>
                                    <span class="text-xs text-gray-400">/ {{ $book->total_copies }}</span>
                                </td>
                                {{-- Disponibilité --}}
                                <td class="px-6 py-3 text-center">
                                    @if($book->available_copies > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Disponible</span>
                                    @elseif($book->total_copies > 1)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Tout emprunté</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Indisponible</span>
                                    @endif
                                </td>
                                {{-- Actions --}}
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('biblio.books.show', $book) }}"
                                           class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                           title="Voir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('biblio.books.edit', $book) }}"
                                           class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('biblio.books.destroy', $book) }}"
                                              onsubmit="return confirm('Supprimer « {{ addslashes($book->title) }} » ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                    <p class="mt-3 text-sm text-gray-500">Aucun livre trouvé.</p>
                                    <a href="{{ route('biblio.books.create') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">Ajouter le premier livre →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($books->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
