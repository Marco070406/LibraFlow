<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with test accounts.
     */
    public function run(): void
    {
        // Administrateur
        $admin = User::create([
            'name' => 'Admin LibraFlow',
            'email' => 'admin@libraflow.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+221 77 000 0001',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Bibliothécaire
        $biblio = User::create([
            'name' => 'Marie Diallo',
            'email' => 'biblio@libraflow.local',
            'password' => Hash::make('password'),
            'role' => 'bibliothecaire',
            'phone' => '+221 77 000 0002',
            'email_verified_at' => now(),
        ]);
        $biblio->assignRole('bibliothecaire');

        // Lecteurs
        $lecteurs = [
            [
                'name' => 'Moussa Ndiaye',
                'email' => 'moussa@libraflow.local',
                'phone' => '+221 77 100 0001',
            ],
            [
                'name' => 'Fatou Sow',
                'email' => 'fatou@libraflow.local',
                'phone' => '+221 77 100 0002',
            ],
            [
                'name' => 'Ibrahima Fall',
                'email' => 'ibrahima@libraflow.local',
                'phone' => '+221 77 100 0003',
            ],
        ];

        foreach ($lecteurs as $data) {
            $lecteur = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'lecteur',
                'phone' => $data['phone'],
                'email_verified_at' => now(),
            ]);
            $lecteur->assignRole('lecteur');
        }
    }
}
