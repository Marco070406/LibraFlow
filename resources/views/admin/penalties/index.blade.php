<x-app-layout>
    <x-slot name="header">Pénalités — Lecteurs redevables</x-slot>

    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Pénalités non payées</h2>
                <p class="text-sm text-gray-400 mt-0.5">Lecteurs ayant des pénalités de retard à régler.</p>
            </div>
            @if($grouped->isNotEmpty())
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                    </svg>
                    {{ $grouped->count() }} lecteur(s) en défaut —
                    {{ number_format($grouped->sum('balance'), 2) }} DA total dû
                </div>
            @endif
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

        @if($grouped->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center py-20 text-gray-400">
                <svg class="w-14 h-14 mb-3 text-emerald-400 opacity-70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-semibold text-emerald-600">Aucune pénalité en attente 🎉</p>
                <p class="text-xs mt-1">Tous les lecteurs sont à jour.</p>
            </div>
        @else
            {{-- Un bloc par lecteur --}}
            @foreach($grouped as $row)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                     x-data="{ open: false }">

                    {{-- En-tête lecteur --}}
                    <div class="px-6 py-4 flex items-center gap-4 cursor-pointer hover:bg-gray-50 transition-colors"
                         @click="open = !open">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($row['user']->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800">{{ $row['user']->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $row['user']->email ?? '' }}</p>
                        </div>
                        {{-- Totaux --}}
                        <div class="text-right flex-shrink-0 mr-2">
                            <p class="text-sm font-bold text-red-600">{{ number_format($row['balance'], 2) }} DA</p>
                            <p class="text-xs text-gray-400">à payer (/ {{ number_format($row['total'], 2) }} DA)</p>
                        </div>
                        {{-- Toggle --}}
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform"
                             :class="open ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    {{-- Détail des emprunts --}}
                    <div x-show="open" x-cloak class="border-t border-gray-100">
                        @foreach($row['loans'] as $loan)
                            @php
                                $loanPaid    = $loan->penaltyPayments->sum('amount_paid');
                                $loanBalance = max(0, $loan->penalty_amount - $loanPaid);
                            @endphp
                            @if($loanBalance > 0)
                                <div class="px-6 py-4 border-b border-gray-50 last:border-b-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                        {{-- Info livre --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800">{{ $loan->book->title ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">
                                                Retourné le {{ $loan->returned_at?->format('d/m/Y') }} —
                                                pénalité totale : <span class="font-semibold text-gray-600">{{ number_format($loan->penalty_amount, 2) }} DA</span>
                                                @if($loanPaid > 0)
                                                    — déjà payé : <span class="font-semibold text-emerald-600">{{ number_format($loanPaid, 2) }} DA</span>
                                                @endif
                                            </p>
                                        </div>
                                        {{-- Solde --}}
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-sm font-bold {{ $loanBalance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                                {{ number_format($loanBalance, 2) }} DA restant
                                            </p>
                                        </div>
                                        {{-- Formulaire paiement --}}
                                        <div x-data="{ paying: false }" class="flex-shrink-0">
                                            <button @click="paying = !paying"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-50 active:scale-95 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Marquer payé
                                            </button>

                                            <div x-show="paying" x-cloak class="mt-3">
                                                <form method="POST"
                                                      action="{{ route('admin.penalties.markPaid', $loan) }}"
                                                      class="flex flex-col gap-2">
                                                    @csrf
                                                    <div class="flex items-center gap-2">
                                                        <input type="number" name="amount_paid"
                                                               value="{{ number_format($loanBalance, 2, '.', '') }}"
                                                               min="0.01" step="0.01"
                                                               placeholder="Montant (DA)"
                                                               class="w-36 px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                                        <button type="submit"
                                                                class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 active:scale-95 transition-all">
                                                            Confirmer
                                                        </button>
                                                    </div>
                                                    <input type="text" name="notes"
                                                           placeholder="Notes (optionnel)"
                                                           class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</x-app-layout>
