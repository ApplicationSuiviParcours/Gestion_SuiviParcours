<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eleve;
use Illuminate\Support\Str;

class EleveSeeder extends Seeder
{
    public function run()
    {
        $noms = ['Koumba', 'Mbala', 'Okombi', 'Mabiala', 'Nkosi', 'Loposso', 'Mayala', 'Moussavou', 'Tchikaya', 'Sassou'];
        $prenoms = ['Jean', 'Marie', 'Paul', 'Alice', 'David', 'Emma', 'Pierre', 'Sophie', 'Marc', 'Clara'];
        $lieux = ['Brazzaville', 'Pointe-Noire', 'Dolisie', 'Nkayi', 'Owando'];

        for ($i = 1; $i <= 10; $i++) {
            $nom = $noms[array_rand($noms)];
            $prenom = $prenoms[array_rand($prenoms)];
            $genre = rand(0,1) ? 'M' : 'F';
            $date_naissance = now()->subYears(rand(10,18))->subDays(rand(0,365));
            $lieu_naissance = $lieux[array_rand($lieux)];
            $adresse = "Quartier ".Str::random(5);
            $telephone = '0670'.rand(10000,99999);
            $email = strtolower($prenom.'.'.$nom.'@example.com');

            Eleve::create([
                'matricule' => 'ELEVE'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'nom' => $nom,
                'prenom' => $prenom,
                'genre' => $genre,
                'date_naissance' => $date_naissance,
                'lieu_naissance' => $lieu_naissance,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'email' => $email,
                'photo' => null,
                'statut' => 'actif',
            ]);
        }
    }
}
