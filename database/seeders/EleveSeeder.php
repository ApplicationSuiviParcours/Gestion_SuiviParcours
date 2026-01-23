<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eleve;

class EleveSeeder extends Seeder
{
    public function run()
    {
        Eleve::create([
            'matricule' => 'ELEVE001',
            'nom' => 'Koumba',
            'prenom' => 'Jean',
            'genre' => 'M',
            'date_naissance' => '2012-05-12',
            'lieu_naissance' => 'Brazzaville',
            'adresse' => 'Quartier Massengo',
            'telephone' => '067054207',
            'email' => 'jean.koumba@example.com',
            'photo' => null,
            'statut' => 'actif',
        ]);
    }
}
