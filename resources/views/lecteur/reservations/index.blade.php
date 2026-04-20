<x-app-layout>
    <x-slot name="header">Mes réservations</x-slot>

    <div class="space-y-6">

        <div>
            <h2 class="text-xl font-bold text-gray-800">Mes réservations</h2>
            <p class="text-sm text-gray-400 mt-0.5">Suivez l'état de vos demandes de réservation.</p>
        </div>

        {{-- Alertes réservations notifiées --}}
        @foreach($reservations as $reservation)
            @if($reservation->status === 'notifie')
                @php
                    $expireAt = $reservation->notified_at?->addDays(3);
                    $daysLeft = max(0, (int) now()->diffInDays($expireAt, false));
                @endphp
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-300 shadow-sm">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-2xl">
                        
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-emerald-800">
                            Un exemplaire de <span class="italic">{{ $reservation->book->title ?? 'ce livre' }}</span> est disponible pour vous !
                        </p>
                        <p class="text-sm text-emerald-700 mt-1">
                            Vous avez jusqu'au
                            <strong>{{ $expireAt?->translatedFormat('l d F Y') }}</strong>
                            pour venir l'emprunter à la bibliothèque.
                            @if($daysLeft <= 1)
                                <span class="ml-1 font-bold text-orange-600">⏰ Dernier jour !</span>
                            @else
                                <span class="ml-1 text-emerald-600">(encore {{ $daysLeft }} jours)</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Alerte info --}}
        @if(session('info'))
            <div class="flex items-center gap-3 p-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-800">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <p class="text-sm font-medium flex-1">{{ session('info') }}</p>
            </div>
        @endif

        {{-- Tableau des réservations --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($reservations->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">Aucune réservation</p>
                    <p class="text-xs mt-1">Explorez le catalogue pour réserver un livre indisponible.</p>
                    <a href="{{ route('lecteur.books.index') }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
                        Explorer le catalogue
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Livre</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Réservé le</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-800">{{ $reservation->book->title ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $reservation->book->author ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $reservation->reserved_at?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($reservation->status === 'en_attente')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                                En attente
                                            </span>
                                        @elseif($reservation->status === 'notifie')
                                            @php $expireAt = $reservation->notified_at?->addDays(3); @endphp
                                            <div>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    Disponible — à récupérer
                                                </span>
                                                @if($expireAt)
                                                    <p class="text-xs text-gray-400 mt-0.5">Expire le {{ $expireAt->format('d/m/Y') }}</p>
                                                @endif
                                            </div>
                                        @elseif($reservation->status === 'annule')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                Annulée
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if(in_array($reservation->status, ['en_attente', 'notifie']))
                                            <form method="POST" action="{{ route('lecteur.reservations.destroy', $reservation) }}"
                                                  onsubmit="return confirm('Annuler cette réservation ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50 active:scale-95 transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Annuler
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

                @if($reservations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $reservations->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
