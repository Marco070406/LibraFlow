<x-app-layout>
    <x-slot name="header">Gestion des emprunts</x-slot>

    <div class="space-y-6">

        {{-- En-tête avec bouton créer --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Emprunts</h2>
                <p class="text-sm text-gray-400 mt-0.5">Gérez les emprunts, retours et retards de la bibliothèque</p>
            </div>
            <a href="{{ route('biblio.loans.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:scale-95 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nouvel emprunt
            </a>
        </div>

        {{-- Onglets de filtre --}}
        <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
            @foreach(['actifs' => 'Actifs', 'retard' => 'En retard', 'retournes' => 'Historique'] as $key => $label)
                <a href="{{ route('biblio.loans.index', ['scope' => $key]) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-all
                          {{ $scope === $key
                              ? 'bg-white text-indigo-700 shadow-sm'
                              : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                    @if($key === 'retard' && $overdueCount > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-xs bg-red-100 text-red-600 rounded-full font-bold">
                            {{ $overdueCount }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($loans->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <p class="text-sm font-medium">Aucun emprunt trouvé</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Livre</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lecteur</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Emprunté le</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Retour prévu</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($loans as $loan)
                                @php
                                    $isOverdue = $loan->isOverdue();
                                    $daysLate = $isOverdue ? (int) $loan->due_at->diffInDays(now()) : 0;
                                @endphp
                                <tr class="{{ $isOverdue ? 'bg-red-50/60 hover:bg-red-50' : 'hover:bg-gray-50' }} transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($isOverdue)
                                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                                </svg>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800 line-clamp-1">{{ $loan->book->title ?? '—' }}</p>
                                                <p class="text-xs text-gray-400">{{ $loan->book->author ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-700">{{ $loan->user->name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->user->email ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $loan->borrowed_at?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                            {{ $loan->due_at?->format('d/m/Y') ?? '—' }}
                                        </span>
                                        @if($isOverdue)
                                            <p class="text-xs text-red-500 font-medium">{{ $daysLate }} j. de retard</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($loan->returned_at)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                Rendu le {{ $loan->returned_at->format('d/m/Y') }}
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                En retard
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                                En cours
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if(!$loan->returned_at)
                                            <form method="POST" action="{{ route('biblio.loans.return', $loan) }}"
                                                  onsubmit="return confirm('Confirmer le retour de ce livre ?')">
                                                @csrf
                                                @method('POST')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
                                                               {{ $isOverdue ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}
                                                               transition-colors active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                                                    </svg>
                                                    Retour
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($loans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $loans->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-app-layout>
