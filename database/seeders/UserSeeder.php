<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'administrateur
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@ecommerce.com',
            'mot_de_passe' => Hash::make('admin123'),
            'telephone' => '01 23 45 67 89',
            'adresse' => '123 Rue de l\'Administration',
            'ville' => 'Paris',
            'code_postal' => '75001',
            'pays' => 'France',
            'role' => 'admin',
            'actif' => true,
            'email_verifie_le' => now(),
        ]);

        // Créer un client de test
        User::create([
            'nom' => 'Hanane',
            'prenom' => 'Abderemane',
            'email' => 'client@test.com',
            'mot_de_passe' => Hash::make('client123'),
            'telephone' => '06 12 34 56 78',
            'adresse' => '456 Avenue des Clients',
            'ville' => 'Lyon',
            'code_postal' => '69000',
            'pays' => 'France',
            'role' => 'client',
            'actif' => true,
            'email_verifie_le' => now(),
        ]);

        // Créer des clients supplémentaires
        User::factory(10)->create([
            'role' => 'client',
            'actif' => true,
            'email_verifie_le' => now(),
        ]);
    }
}
