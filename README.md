# LibraFlow – Système de Gestion de Bibliothèque

LibraFlow est une application web de gestion de bibliothèque développée avec **Laravel 12**.
Elle permet la gestion des livres, des emprunts, des réservations et des notifications automatiques par email.

---

## Prérequis

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- Node.js & npm

---

## Installation

```bash
# 1. Cloner le dépôt
git clone <url-du-repo> libraflow
cd libraflow

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install && npm run build

# 4. Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Exécuter les migrations et les seeders
php artisan migrate --seed
```

---

## Lancer le serveur de développement

```bash
php artisan serve
```

---

## Système de Notifications Email

LibraFlow envoie automatiquement trois types d'emails :

| Email | Déclencheur | Description |
|---|---|---|
| **Livre disponible** | Retour d'un livre réservé | Notifie le prochain lecteur en file d'attente |
| **Rappel J-2** | Tâche planifiée à 08h00 | Rappel préventif 2 jours avant la date de retour |
| **Rappel retard** | Tâche planifiée à 08h00 | Rappel quotidien aux lecteurs en retard |

### Configuration SMTP (`.env`)

```env
MAIL_MAILER=smtp
MAIL_HOST=votre-serveur-smtp
MAIL_PORT=587
MAIL_USERNAME=votre-login
MAIL_PASSWORD=votre-mot-de-passe
MAIL_FROM_ADDRESS=bibliotheque@libraflow.local
MAIL_FROM_NAME="LibraFlow – Bibliothèque"
```

> **Développement local :** Vous pouvez utiliser [Mailpit](https://github.com/axllent/mailpit) (port 1025/8025)
> ou laisser `MAIL_MAILER=log` pour écrire les emails dans `storage/logs/laravel.log`.

---

## Queue (File d'attente)

Les emails sont envoyés de manière asynchrone via le driver **database**.

### Lancer le worker de queue

```bash
php artisan queue:work
```

> Pour la production, utilisez **Supervisor** pour maintenir le worker actif en permanence.

```bash
# Optionnel : lancer avec un délai de retry et un timeout
php artisan queue:work --tries=3 --timeout=60
```

### Vérifier les jobs échoués

```bash
php artisan queue:failed
php artisan queue:retry all
```

---

## Tâches planifiées (Scheduler)

Deux commandes sont planifiées automatiquement :

| Commande | Heure | Rôle |
|---|---|---|
| `reservations:expire` | Quotidien (minuit) | Expire les réservations non honorées après 3 jours |
| `loans:send-reminders` | Quotidien à **08:00** | Envoie les rappels J-2 et les rappels de retard |

### Activer le scheduler

**Sous Linux/macOS** – Ajouter au crontab :

```cron
* * * * * cd /chemin/vers/libraflow && php artisan schedule:run >> /dev/null 2>&1
```

**Sous Windows** – Utiliser le Planificateur de tâches Windows ou lancer manuellement :

```bash
php artisan schedule:run
```

### Tester manuellement les commandes

```bash
# Lancer les rappels manuellement
php artisan loans:send-reminders

# Expirer les réservations
php artisan reservations:expire
```

---

## Rôles utilisateurs

| Rôle | Accès |
|---|---|
| `admin` | Gestion globale, paramètres, rapports de pénalités |
| `bibliothecaire` | Gestion livres, emprunts, réservations |
| `lecteur` | Consultation catalogue, historique personnel |

---

## Architecture des notifications

```
LoanController@returnBook
  └─► SendBookAvailableNotification::dispatch($reservation)
        └─► [Queue Worker] Mail::to(user)->send(BookAvailableNotification)

Scheduler (08:00)
  └─► loans:send-reminders
        ├─► Mail::to(user)->send(LoanDueSoonReminder)   # J-2, envoi direct
        └─► SendOverdueReminders::dispatch()             # Via queue
              └─► [Queue Worker] Mail::to(user)->send(LoanOverdueReminder)
```

---

## Licence

Projet académique – GLSI Semestre 6 · Outils de Développement Web
# LibraFlow
