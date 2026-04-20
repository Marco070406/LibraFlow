{{--
    Composant : penalty-alert
    Encadré pénalité bienveillant — jamais accusateur.

    Paramètres :
      - $loan        : App\Models\Loan
      - $penalty     : float  (montant en FCFA)
--}}
@props(['loan', 'penalty'])

<div class="flex items-start gap-4 p-4 rounded-2xl border-l-4"
     style="background-color: #fefce8; border-color: #ca8a04;">

    {{-- Icône information, PAS triangle warning --}}
    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center"
         style="background-color: #fef9c3;">
        <svg class="w-5 h-5" style="color: #a16207;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        </svg>
    </div>

    <div class="flex-1 min-w-0">
        {{-- Titre bienveillant —   PAS de majuscules agressives --}}
        <p class="text-sm font-semibold" style="color: #78350f;">Un petit rappel 📚</p>

        <p class="text-sm mt-1" style="color: #92400e;">
            Votre exemplaire de
            <span class="font-medium italic">{{ $loan->book->title ?? 'ce livre' }}</span>
            était attendu le {{ $loan->due_at?->translatedFormat('d F Y') }}.
            @if($penalty > 0)
                Pour éviter que la pénalité n'augmente davantage
                (actuellement <span class="font-semibold">{{ number_format($penalty, 0, ',', ' ') }} FCFA</span>),
                pensez à le rapporter dès que vous le pouvez.
            @else
                Pensez à le rapporter à la bibliothèque dès que vous le pouvez.
            @endif
            Merci !
        </p>
    </div>
</div>
