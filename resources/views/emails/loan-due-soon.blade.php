@extends('emails.layouts.base')

@section('title', 'Rappel de retour dans 2 jours – LibraFlow')

@section('content')
    <h1>Votre retour approche !</h1>

    <p>Bonjour <strong>{{ $loan->user->first_name ?? $loan->user->name }}</strong>,</p>

    <p>
        Nous vous rappelons amicalement que votre emprunt arrive bientôt à échéance.
        Il ne vous reste plus que <strong>2 jours</strong> pour rapporter votre livre à la bibliothèque.
    </p>

    <div class="info-box">
        <p class="info-label">Emprunt concerné</p>
        <p><strong>{{ $loan->book->title }}</strong></p>
        @if($loan->book->author ?? false)
            <p style="margin-top: 4px; font-size: 13px; color: #6b7280;">{{ $loan->book->author }}</p>
        @endif
        <p style="margin-top: 10px;">
            Date limite de retour :
            <strong>{{ $loan->due_at->translatedFormat('l j F Y') }}</strong>
        </p>
    </div>

    <p>
        Pour éviter toute pénalité, pensez à rapporter ce livre à la bibliothèque
        avant la date indiquée ci-dessus. Un retour dans les temps vous permet
        également de réserver d'autres ouvrages sans restriction.
    </p>

    <p>
        Merci pour votre fidélité et à bientôt à la bibliothèque ! 
    </p>

    <hr class="divider">

    <p style="font-size: 13px; color: #9ca3af; margin-bottom: 0;">
        Ce rappel préventif est envoyé automatiquement 2 jours avant la date de retour prévue.
        Si vous avez déjà rendu ce livre, veuillez ignorer ce message.
    </p>
@endsection
