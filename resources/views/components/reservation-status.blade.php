{{--
    Composant : reservation-status
    Badge de statut de réservation.

    Paramètres :
      - $reservation : App\Models\Reservation
      - $showPosition : bool (affiche la position dans la file, défaut: false)
--}}
@props(['reservation', 'showPosition' => false])

@php
    $status = $reservation->status;

    [$bg, $text, $label] = match($status) {
        'en_attente' => ['bg-amber-100',  'text-amber-700',  'En attente'],
        'notifie'    => ['bg-blue-100',   'text-blue-700',   'Disponible !'],
        'expire'     => ['bg-gray-100',   'text-gray-500',   'Expiré'],
        'annule'     => ['bg-red-50',     'text-red-500',    'Annulé'],
        default      => ['bg-gray-100',   'text-gray-500',   $status],
    };

    $dot = match($status) {
        'en_attente' => 'bg-amber-500',
        'notifie'    => 'bg-blue-500',
        'expire'     => 'bg-gray-400',
        'annule'     => 'bg-red-400',
        default      => 'bg-gray-400',
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $bg }} {{ $text }}">
    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $dot }}"></span>
    {{ $label }}
</span>
