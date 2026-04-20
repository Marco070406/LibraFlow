{{--
    Composant : availability-badge
    Paramètres :
      - $book : App\Models\Book
      - $size : 'sm' | 'md' (défaut: 'md')
--}}
@props(['book', 'size' => 'md'])

@php
    $available = $book->available_copies > 0;
    $label     = $available ? ($book->available_copies . ' dispo') : 'Indisponible';
    $bg        = $available ? 'bg-emerald-100' : 'bg-gray-100';
    $text      = $available ? 'text-emerald-700' : 'text-gray-500';
    $dot       = $available ? 'bg-emerald-500' : 'bg-gray-400';
    $padding   = $size === 'sm' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs';
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full font-medium {{ $bg }} {{ $text }} {{ $padding }}">
    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $dot }}"></span>
    {{ $label }}
</span>
