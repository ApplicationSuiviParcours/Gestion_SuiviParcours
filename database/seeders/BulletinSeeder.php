<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bulletin;
use App\Models\Eleve;
use App\Models\AnneeScolaire;


class BulletinSeeder extends Seeder
{
    public function run()
    {
        $eleves = Eleve::all();
        $annees = AnneeScolaire::all();

        foreach($eleves as $eleve){
            foreach($annees as $annee){
                Bulletin::create([
                    'eleve_id' => $eleve->id,
                    'annee_id' => $annee->id,
                    'moyenne_generale' => rand(10,20),
                    'appreciation' => 'Très bon travail',
                ]);
            }
        }
    }
}

