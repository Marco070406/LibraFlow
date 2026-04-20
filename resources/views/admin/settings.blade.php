<x-app-layout>
    <x-slot name="header">Paramètres globaux</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        <div>
            <h2 class="text-xl font-bold text-gray-800">Paramètres de la bibliothèque</h2>
            <p class="text-sm text-gray-400 mt-0.5">Ces valeurs s'appliquent à tous les nouveaux emprunts et calculs de pénalités.</p>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf

            {{-- Durée d'emprunt --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <label for="loan_duration_days" class="block text-sm font-semibold text-gray-800">Durée d'emprunt (jours)</label>
                        <p class="text-xs text-gray-500 mt-0.5">Nombre de jours accordés à un lecteur avant qu'un emprunt soit considéré en retard. Les emprunts existants ne sont pas affectés.</p>
                    </div>
                </div>
                <div class="ml-14">
                    <input type="number" id="loan_duration_days" name="loan_duration_days"
                           value="{{ old('loan_duration_days', $loanDays) }}"
                           min="1" max="60"
                           class="w-32 px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('loan_duration_days') border-red-400 @enderror">
                    <span class="ml-2 text-sm text-gray-500">jours (1–60)</span>
                    @error('loan_duration_days')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Pénalité journalière --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <label for="daily_penalty" class="block text-sm font-semibold text-gray-800">Pénalité journalière (DA)</label>
                        <p class="text-xs text-gray-500 mt-0.5">Montant facturé par jour de retard. Ce tarif est appliqué en temps réel au calcul des pénalités pour tous les emprunts en retard en cours.</p>
                    </div>
                </div>
                <div class="ml-14">
                    <input type="number" id="daily_penalty" name="daily_penalty"
                           value="{{ old('daily_penalty', $dailyPenalty) }}"
                           min="0" step="0.01"
                           class="w-32 px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('daily_penalty') border-red-400 @enderror">
                    <span class="ml-2 text-sm text-gray-500">DA / jour</span>
                    @error('daily_penalty')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bouton --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:scale-95 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Enregistrer les paramètres
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
