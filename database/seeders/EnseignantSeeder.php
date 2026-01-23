<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enseignant;

class EnseignantSeeder extends Seeder
{
    public function run()
    {
        Enseignant::create([
            'nom' => 'Mvoula',
            'prenom' => 'Pierre',
            'telephone' => '067123456',
            'email' => 'pierre.mvoula@example.com',
        ]);
    }
}
