@extends('emails.layouts.base')

@section('title', 'Rappel de retour – LibraFlow')

@section('content')
    <h1>Un petit rappel de notre part</h1>

    <p>Bonjour <strong>{{ $loan->user->first_name ?? $loan->user->name }}</strong>,</p>

    <p>
        Nous espérons sincèrement que vous avez apprécié votre lecture de
        <em>« {{ $loan->book->title }} »</em>.
        C'est avec bienveillance que nous vous contactons pour vous rappeler
        que cet emprunt est arrivé à échéance.
    </p>

    <div class="info-box">
        <p class="info-label">Détails de l'emprunt</p>
        <p><strong>{{ $loan->book->title }}</strong></p>
        @if($loan->book->author ?? false)
            <p style="margin-top: 4px; font-size: 13px; color: #6b7280;">{{ $loan->book->author }}</p>
        @endif
        <p style="margin-top: 10px;">
            Date de retour prévue :
            <strong>{{ $loan->due_at->translatedFormat('l j F Y') }}</strong>
        </p>
        <p>
            Jours de retard : <strong>{{ $daysOverdue }} jour{{ $daysOverdue > 1 ? 's' : '' }}</strong>
        </p>
        <p>
            Pénalité actuelle :
            <strong style="color: #dc2626;">{{ number_format($penaltyAmount, 0, ',', ' ') }} FCFA</strong>
        </p>
    </div>

    <p>
        Nous comptons sur vous pour rapporter ce livre dès que possible à la bibliothèque.
        La pénalité continuera de s'accumuler chaque jour supplémentaire de retard.
    </p>

    <p>
        Si vous rencontrez une difficulté particulière, n'hésitez pas à contacter
        directement le service bibliothèque — nous trouverons une solution ensemble.
    </p>

    <hr class="divider">

    <p>Merci de votre compréhension et à très bientôt à la bibliothèque !</p>

    <p style="font-size: 13px; color: #9ca3af; margin-top: 16px; margin-bottom: 0;">
        Ce rappel est envoyé automatiquement. La pénalité est calculée à raison de
        {{ number_format(\App\Models\Setting::get('daily_penalty', config('libraflow.daily_penalty', 100)), 0, ',', ' ') }} FCFA par jour de retard.
    </p>
@endsection
