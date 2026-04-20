<x-app-layout>
    <x-slot name="header">Emprunts en retard</x-slot>

    <div class="space-y-6">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    Emprunts en retard
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">Pénalité journalière appliquée : <strong class="text-gray-700">{{ number_format($dailyPenalty, 0) }} DA / jour</strong></p>
            </div>
            <a href="{{ route('biblio.loans.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Retour aux emprunts
            </a>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($loans->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mb-3 text-emerald-400 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-emerald-600">Aucun retard en cours 🎉</p>
                    <p class="text-xs text-gray-400 mt-1">Tous les livres ont été retournés à temps.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-red-50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Livre</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lecteur</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Retour prévu</th>
                                <th class="text-center px-6 py-3 text-xs font-semibold text-red-600 uppercase tracking-wider">Jours de retard</th>
                                <th class="text-center px-6 py-3 text-xs font-semibold text-orange-600 uppercase tracking-wider">Pénalité estimée</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($loans as $loan)
                                @php
                                    $daysLate = (int) $loan->due_at->diffInDays(now());
                                    $penalty  = $daysLate * $dailyPenalty;
                                @endphp
                                <tr class="bg-red-50/40 hover:bg-red-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-800 line-clamp-1">{{ $loan->book->title ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->book->author ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-700">{{ $loan->user->name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->user->email ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-red-600">{{ $loan->due_at?->format('d/m/Y') ?? '—' }}</span>
                                        <p class="text-xs text-gray-400">{{ $loan->due_at?->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-700 font-bold text-sm">
                                            {{ $daysLate }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold text-sm">
                                            {{ number_format($penalty, 0) }} DA
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('biblio.loans.return', $loan) }}"
                                              onsubmit="return confirm('Confirmer le retour et appliquer la pénalité de {{ number_format($penalty, 0) }} DA ?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 active:scale-95 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                                                </svg>
                                                Enregistrer le retour
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($loans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $loans->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
