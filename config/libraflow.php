<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Durée d'emprunt par défaut (en jours)
    |--------------------------------------------------------------------------
    |
    | Nombre de jours accordés pour un emprunt de livre avant qu'il ne soit
    | considéré comme en retard.
    |
    */

    'loan_duration_days' => env('LIBRAFLOW_LOAN_DURATION_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Pénalité journalière de retard (en FCFA)
    |--------------------------------------------------------------------------
    |
    | Montant facturé par jour de retard pour un livre non retourné à temps.
    |
    */

    'daily_penalty' => env('LIBRAFLOW_DAILY_PENALTY', 100),

    /*
    |--------------------------------------------------------------------------
    | Devise
    |--------------------------------------------------------------------------
    |
    | Symbole de la devise utilisée pour l'affichage des pénalités.
    |
    */

    'currency' => 'FCFA',

    /*
    |--------------------------------------------------------------------------
    | Stockage des couvertures de livres
    |--------------------------------------------------------------------------
    |
    | Disque et dossier utilisés pour stocker les images de couverture.
    |
    */

    'covers_disk' => 'public',
    'covers_path' => 'covers',

];
