<x-app-layout>
    <x-slot name="header">Gestion des utilisateurs</x-slot>

    <div class="space-y-6" x-data="{ modal: null, selectedRole: '' }">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Utilisateurs</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $users->total() }} compte(s) enregistré(s)</p>
            </div>
        </div>

        {{-- Flash --}}
        @foreach(['success','error','warning'] as $type)
            @if(session($type))
                @php $colors = ['success'=>'emerald','error'=>'red','warning'=>'amber']; $c = $colors[$type]; @endphp
                <div class="flex items-center gap-3 p-4 rounded-xl bg-{{ $c }}-50 border border-{{ $c }}-200 text-{{ $c }}-800">
                    <p class="text-sm font-medium">{{ session($type) }}</p>
                </div>
            @endif
        @endforeach

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rôle</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Emprunts actifs</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Inscrit le</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            @php
                                $roleColors = [
                                    'admin'         => 'bg-purple-100 text-purple-700',
                                    'bibliothecaire'=> 'bg-amber-100 text-amber-700',
                                    'lecteur'       => 'bg-emerald-100 text-emerald-700',
                                ];
                                $roleLabels = [
                                    'admin'         => 'Admin',
                                    'bibliothecaire'=> 'Bibliothécaire',
                                    'lecteur'       => 'Lecteur',
                                ];
                                $rc = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600';
                                $rl = $roleLabels[$user->role] ?? $user->role;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Avatar + nom --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 truncate">{{ $user->name }}
                                                @if($user->id === auth()->id())
                                                    <span class="ml-1 text-xs text-gray-400">(vous)</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                {{-- Rôle --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $rc }}">{{ $rl }}</span>
                                </td>
                                {{-- Emprunts actifs --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm {{ $user->active_loans_count > 0 ? 'font-semibold text-amber-600' : 'text-gray-400' }}">
                                        {{ $user->active_loans_count }}
                                    </span>
                                </td>
                                {{-- Date inscription --}}
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    {{ $user->created_at?->format('d/m/Y') }}
                                </td>
                                {{-- Action --}}
                                <td class="px-6 py-4 text-right">
                                    @if($user->id !== auth()->id())
                                        <button
                                            id="role-btn-{{ $user->id }}"
                                            @click="modal = {{ $user->id }}; selectedRole = '{{ $user->role }}'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 active:scale-95 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                            </svg>
                                            Changer le rôle
                                        </button>

                                        {{-- Modal Alpine.js inline --}}
                                        <div x-show="modal === {{ $user->id }}"
                                             x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                             @keydown.escape.window="modal = null">
                                            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="modal = null"></div>
                                            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4" @click.stop>
                                                <h3 class="text-base font-bold text-gray-800">Changer le rôle</h3>
                                                <p class="text-sm text-gray-500">Modifier le rôle de <strong>{{ $user->name }}</strong> :</p>

                                                <form method="POST" action="{{ route('admin.users.role', $user) }}" class="space-y-4">
                                                    @csrf
                                                    <div class="space-y-2">
                                                        @foreach(['admin' => ['Admin','bg-purple-100 text-purple-700'], 'bibliothecaire' => ['Bibliothécaire','bg-amber-100 text-amber-700'], 'lecteur' => ['Lecteur','bg-emerald-100 text-emerald-700']] as $val => [$label, $cls])
                                                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors"
                                                                   :class="selectedRole === '{{ $val }}' ? 'border-indigo-400 bg-indigo-50' : ''">
                                                                <input type="radio" name="role" value="{{ $val }}"
                                                                       x-model="selectedRole"
                                                                       class="accent-indigo-600">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cls }}">{{ $label }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    <div class="flex gap-3 pt-2">
                                                        <button type="button" @click="modal = null"
                                                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                                                            Annuler
                                                        </button>
                                                        <button type="submit"
                                                                class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 active:scale-95 transition-all">
                                                            Confirmer
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-gray-400 text-sm">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
