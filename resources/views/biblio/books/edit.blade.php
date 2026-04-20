<x-app-layout>
    <x-slot name="header">Modifier « {{ $book->title }} »</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Modifier le livre</h3>
                <p class="text-sm text-gray-500 mt-0.5">Modifiez les informations du livre ci-dessous.</p>
            </div>

            <form method="POST" action="{{ route('biblio.books.update', $book) }}" enctype="multipart/form-data" class="p-6">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Colonne gauche : informations --}}
                    <div class="lg:col-span-2 space-y-5">
                        {{-- Titre --}}
                        <div>
                            <x-input-label for="title" :value="__('Titre')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $book->title)" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        {{-- Auteur --}}
                        <div>
                            <x-input-label for="author" :value="__('Auteur')" />
                            <x-text-input id="author" name="author" type="text" class="mt-1 block w-full" :value="old('author', $book->author)" required />
                            <x-input-error :messages="$errors->get('author')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- ISBN --}}
                            <div>
                                <x-input-label for="isbn" :value="__('ISBN (optionnel)')" />
                                <x-text-input id="isbn" name="isbn" type="text" class="mt-1 block w-full" :value="old('isbn', $book->isbn)" />
                                <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
                            </div>

                            {{-- Catégorie --}}
                            <div>
                                <x-input-label for="category" :value="__('Catégorie')" />
                                <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $book->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Nombre d'exemplaires --}}
                        <div class="max-w-xs">
                            <x-input-label for="total_copies" :value="__('Nombre d\'exemplaires')" />
                            <x-text-input id="total_copies" name="total_copies" type="number" min="1" max="99" class="mt-1 block w-full" :value="old('total_copies', $book->total_copies)" required />
                            <x-input-error :messages="$errors->get('total_copies')" class="mt-2" />
                            <p class="text-xs text-gray-400 mt-1">Actuellement {{ $book->available_copies }} / {{ $book->total_copies }} disponible(s).</p>
                        </div>

                        {{-- Description --}}
                        <div>
                            <x-input-label for="description" :value="__('Description (optionnel)')" />
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">{{ old('description', $book->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Colonne droite : couverture --}}
                    <div class="space-y-4">
                        <x-input-label :value="__('Couverture')" />
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center"
                             x-data="{ preview: '{{ $book->cover_image ? asset('storage/' . $book->cover_image) : '' }}' }">
                            {{-- Aperçu actuel ou placeholder --}}
                            <div x-show="!preview" class="py-6">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                </svg>
                                <p class="mt-2 text-xs text-gray-400">Aucune couverture</p>
                            </div>
                            <img x-show="preview" :src="preview" class="max-h-56 mx-auto rounded-lg object-cover" x-cloak>
                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                                   class="mt-3 block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"
                                   @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = e => preview = e.target.result; reader.readAsDataURL(file); }">
                            <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                    <a href="{{ route('biblio.books.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">Annuler</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
