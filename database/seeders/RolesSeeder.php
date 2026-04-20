<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Seed the roles table with the 3 LibraFlow roles.
     */
    public function run(): void
    {
        $roles = ['admin', 'bibliothecaire', 'lecteur'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
