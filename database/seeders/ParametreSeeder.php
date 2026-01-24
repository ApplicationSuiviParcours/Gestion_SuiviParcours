<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parametre;

class ParametreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    Parametre::updateOrCreate(
        ['cle' => 'nom_ecole'],
        ['valeur' => 'SmartSchool']
    );

    Parametre::updateOrCreate(
        ['cle' => 'email_contact'],
        ['valeur' => 'contact@smartschool.com']
    );

    Parametre::updateOrCreate(
        ['cle' => 'telephone'],
        ['valeur' => '+242 06 700 0000']
    );
    }
}
