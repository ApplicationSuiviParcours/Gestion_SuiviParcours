<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Note;

class NoteSeeder extends Seeder
{
    public function run()
    {
        Note::create([
            'eleve_id' => 1,
            'evaluation_id' => 1,
            'valeur' => 15,
        ]);
    }
}
