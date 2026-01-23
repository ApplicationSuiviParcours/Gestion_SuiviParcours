<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evaluation;

class EvaluationSeeder extends Seeder
{
    public function run()
    {
        Evaluation::create([
            'type_evaluation' => 'devoir',
            'date_evaluation' => now(),
            'classe_id' => 1,
            'matiere_id' => 1,
            'annee_id' => 1,
        ]);
    }
}