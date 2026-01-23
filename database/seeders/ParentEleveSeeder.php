<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentEleve;

class ParentEleveSeeder extends Seeder
{
    public function run()
    {
        ParentEleve::create([
            'nom' => 'Koumba',
            'prenom' => 'Marie',
            'telephone' => '067746860',
            'email' => 'marie.koumba@example.com',
            'adresse' => 'Brazzaville',
        ]);
    }
}
