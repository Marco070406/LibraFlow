{{--
    Composant : loan-progress-bar
    Affiche la barre de progression vers la date de retour.

    Paramètres :
      - $loan : App\Models\Loan (attributs due_at, borrowed_at requis)

    Couleurs :
      - Vert  (#16a34a) : > 7 jours restants
      - Orange (#d97706) : entre 3 et 7 jours
      - Rouge  (#dc2626) : moins de 3 jours ou en retard
--}}
@props(['loan'])

@php
    $dueAt     = $loan->due_at;
    $isOverdue = $loan->isOverdue();

    if ($isOverdue) {
        $daysLeft   = 0;
        $pct        = 100;
        $barColor   = '#dc2626';
        $textColor  = 'text-red-600';
        $label      = 'En retard de ' . (int) $dueAt->diffInDays(now()) . ' j.';
    } else {
        $daysLeft   = (int) now()->diffInDays($dueAt);
        $totalDays  = max(1, (int) $loan->borrowed_at->diffInDays($dueAt));
        $elapsed    = max(0, $totalDays - $daysLeft);
        $pct        = min(100, round(($elapsed / $totalDays) * 100));

        if ($daysLeft > 7) {
            $barColor  = '#16a34a';
            $textColor = 'text-emerald-700';
        } elseif ($daysLeft >= 3) {
            $barColor  = '#d97706';
            $textColor = 'text-amber-600';
        } else {
            $barColor  = '#dc2626';
            $textColor = 'text-red-600';
        }

        $label = $daysLeft . ' jour' . ($daysLeft > 1 ? 's' : '') . ' restant' . ($daysLeft > 1 ? 's' : '');
    }
@endphp

<div class="w-full space-y-1">
    {{-- Barre --}}
    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
        <div class="h-2 rounded-full transition-all duration-500"
             style="width: {{ $pct }}%; background-color: {{ $barColor }};"></div>
    </div>
    {{-- Label --}}
    <p class="text-xs font-medium {{ $textColor }}">{{ $label }}</p>
</div>
