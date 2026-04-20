<x-app-layout>
    <x-slot name="header">Tableau de bord — Administration</x-slot>

    <div class="space-y-6">

        {{-- Welcome banner --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-lg p-6 text-white">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold">Bienvenue, {{ auth()->user()->name }} </h2>
                <p class="mt-1 text-indigo-100 text-sm">Vue d'ensemble de la bibliothèque — {{ now()->translatedFormat('l d F Y') }}</p>
            </div>
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute -right-4 -bottom-8 w-28 h-28 bg-white/10 rounded-full"></div>
        </div>

        {{-- ── KPI Cards ────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Titres distincts --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Titres</p>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['distinct_titles'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['total_books'] }} exemplaire(s) total</p>
            </div>

            {{-- Emprunts actifs --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h1.5m9 0h-9"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunts actifs</p>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['active_loans'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['returned_this_month'] }} retourné(s) ce mois</p>
            </div>

            {{-- Retards --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">En retard</p>
                </div>
                <p class="text-3xl font-bold {{ $stats['overdue_loans'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $stats['overdue_loans'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['pending_reservations'] }} réservation(s) en attente</p>
            </div>

            {{-- Utilisateurs --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateurs</p>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                <p class="text-xs text-gray-400 mt-1">inscrits</p>
            </div>
        </div>

        {{-- ── Graphiques CSS purs ──────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Top 5 livres les plus empruntés (barres horizontales) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800"> Top 5 livres les plus empruntés</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @forelse($topBooks as $i => $row)
                        @php
                            $pct = $maxLoans > 0 ? round(($row->loan_count / $maxLoans) * 100) : 0;
                            $colors = ['bg-indigo-500','bg-purple-500','bg-emerald-500','bg-amber-500','bg-rose-500'];
                            $color  = $colors[$i] ?? 'bg-gray-400';
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 truncate max-w-[75%]">
                                    {{ $i + 1 }}. {{ $row->book->title ?? '—' }}
                                </span>
                                <span class="text-sm font-bold text-gray-800 ml-2">{{ $row->loan_count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="{{ $color }} h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $row->book->author ?? '' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6">Aucun emprunt enregistré.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top 3 catégories + 5 derniers retards --}}
            <div class="space-y-4">

                {{-- Top 3 catégories (barres + pourcentage) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800"> Top 3 catégories</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        @php $catMax = $topCategories->max('loan_count') ?: 1; @endphp
                        @forelse($topCategories as $i => $cat)
                            @php
                                $pct = round(($cat->loan_count / $catMax) * 100);
                                $bg  = ['bg-teal-500','bg-cyan-500','bg-sky-500'][$i] ?? 'bg-gray-400';
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $cat->category ?: 'Non classé' }}</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $cat->loan_count }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="{{ $bg }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 py-2">Aucune donnée.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Liens rapides --}}
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
                        <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-indigo-700">Utilisateurs</span>
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center gap-3 p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                        <div class="w-9 h-9 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-purple-700">Paramètres</span>
                    </a>
                    <a href="{{ route('admin.penalties.index') }}"
                       class="flex items-center gap-3 p-4 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                        <div class="w-9 h-9 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-red-700">Pénalités</span>
                    </a>
                    <a href="{{ route('biblio.loans.overdue') }}"
                       class="flex items-center gap-3 p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors">
                        <div class="w-9 h-9 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-amber-700">Retards</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Tableau 5 derniers retards ───────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800"> Emprunts en retard (5 plus anciens)</h3>
                @if($stats['overdue_loans'] > 0)
                    <span class="text-xs font-medium bg-red-100 text-red-700 px-2.5 py-0.5 rounded-full">
                        {{ $stats['overdue_loans'] }} au total
                    </span>
                @endif
            </div>
            @if($overdueLoans->isEmpty())
                <div class="flex items-center justify-center gap-2 py-10 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">Aucun retard en cours — tout est à jour !</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Livre</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lecteur</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Échéance</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Retard</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($overdueLoans as $loan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <p class="font-medium text-gray-800">{{ $loan->book->title ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->book->author ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">{{ $loan->user->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-500">{{ $loan->due_at?->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">
                                            {{ $loan->due_at?->diffForHumans() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
