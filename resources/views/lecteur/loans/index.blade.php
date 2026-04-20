<x-app-layout>
    <x-slot name="header">Mes emprunts</x-slot>

    <div class="space-y-6">

        <div>
            <h2 class="text-xl font-bold text-gray-800">Mon historique d'emprunts</h2>
            <p class="text-sm text-gray-400 mt-0.5">Retrouvez ici tous vos livres empruntés et leur statut.</p>
        </div>

        {{-- ── Alertes retard — ton bienveillant, fond jaune doux ── --}}
        @foreach($loans as $loan)
            @if($loan->isOverdue())
                @php $penalty = $loanService->calculatePenalty($loan); @endphp
                <x-penalty-alert :loan="$loan" :penalty="$penalty" />
            @endif
        @endforeach

        {{-- ── Tableau des emprunts ── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($loans->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <p class="text-sm font-medium">Aucun emprunt trouvé</p>
                    <p class="text-xs text-gray-400 mt-1">Vous n'avez pas encore emprunté de livre.</p>
                    <a href="{{ route('lecteur.books.index') }}"
                       class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                        Explorer le catalogue
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Livre</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Emprunté le</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Retour prévu</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($loans as $loan)
                                @php $isOverdue = $loan->isOverdue(); @endphp
                                <tr class="{{ $isOverdue ? '' : 'hover:bg-gray-50' }} transition-colors"
                                    style="{{ $isOverdue ? 'background-color:#fefce8;' : '' }}">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-800">{{ $loan->book->title ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->book->author ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $loan->borrowed_at?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{-- Pas de rouge, couleur neutre ou ambre --}}
                                        <span class="{{ $isOverdue ? 'font-semibold' : 'text-gray-600' }}"
                                              style="{{ $isOverdue ? 'color:#92400e;' : '' }}">
                                            {{ $loan->due_at?->format('d/m/Y') ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($loan->returned_at)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Rendu le {{ $loan->returned_at->format('d/m/Y') }}
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  style="background-color:#fef9c3;color:#92400e;">
                                                📚 À rapporter
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                                En cours
                                            </span>
                                        @endif
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
