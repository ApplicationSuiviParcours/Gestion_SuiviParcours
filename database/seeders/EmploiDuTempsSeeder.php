<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmploiDuTemps;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\User;

class EmploiDuTempsSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $users = User::all(); 

        if ($classes->isEmpty() || $matieres->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Données manquantes : classes, matières ou utilisateurs');
            return;
        }

        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        foreach ($classes as $classe) {
            foreach ($jours as $jour) {
                EmploiDuTemps::create([
                    'classe_id'     => $classe->id,
                    'matiere_id'    => $matieres->random()->id,
                    'enseignant_id' => $users->random()->id, 
                    'jour'          => $jour,
                    'heure_debut'   => '08:00',
                    'heure_fin'     => '09:00',
                ]);
            }
        }

        $this->command->info('Emplois du temps générés avec succès');
    }
}
