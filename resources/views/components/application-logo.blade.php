{{-- Logo LibraFlow : icône SVG d'un livre ouvert --}}
<svg {{ $attributes->merge(['class' => 'fill-current']) }} viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
    {{-- Livre ouvert --}}
    <path d="M40 16C34 12 26 10 16 10C12 10 8 10.5 4 11.5C2.5 11.9 1.5 13.2 1.5 14.7V58.7C1.5 60.8 3.5 62.3 5.5 61.7C9 60.8 12.5 60.3 16 60.3C25 60.3 33 63.3 40 69C47 63.3 55 60.3 64 60.3C67.5 60.3 71 60.8 74.5 61.7C76.5 62.3 78.5 60.8 78.5 58.7V14.7C78.5 13.2 77.5 11.9 76 11.5C72 10.5 68 10 64 10C54 10 46 12 40 16Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
    {{-- Ligne centrale (reliure) --}}
    <path d="M40 16V69" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
    {{-- Lignes de texte (page gauche) --}}
    <path d="M12 24H30M12 32H28M12 40H26M12 48H24" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.5"/>
    {{-- Lignes de texte (page droite) --}}
    <path d="M50 24H68M52 32H68M54 40H68M56 48H68" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.5"/>
</svg>
