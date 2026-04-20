@extends('emails.layouts.base')

@section('title', 'Votre livre est disponible – LibraFlow')

@section('content')
    <h1> Bonne nouvelle !</h1>

    <p>Bonjour <strong>{{ $reservation->user->first_name ?? $reservation->user->name }}</strong>,</p>

    <p>
        Nous avons le plaisir de vous informer que le livre que vous attendiez
        est maintenant disponible à la bibliothèque. C'est votre moment !
    </p>

    <div class="info-box">
        <p class="info-label">Livre disponible</p>
        <p><strong>{{ $reservation->book->title }}</strong></p>
        @if($reservation->book->author ?? false)
            <p style="margin-top: 4px; color: #6b7280; font-size: 13px;">
                {{ $reservation->book->author }}
            </p>
        @endif
    </div>

    <p>
        Vous disposez de <strong>3 jours</strong> pour venir l'emprunter en bibliothèque,
        soit jusqu'au <strong>{{ $deadline->translatedFormat('l j F Y') }}</strong>.
    </p>

    <div class="alert-box">
        <p>
            ⚠️&nbsp; Passé ce délai, votre réservation sera automatiquement annulée
            et le livre sera proposé au prochain lecteur sur la liste d'attente.
        </p>
    </div>

    <p>
        Nous serons ravis de vous accueillir. Bonne lecture en perspective ! 
    </p>

    <hr class="divider">

    <p style="font-size: 13px; color: #9ca3af; margin-bottom: 0;">
        Cette notification vous a été envoyée car vous étiez inscrit en liste d'attente
        pour ce livre sur LibraFlow.
    </p>
@endsection
