<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absence;

class AbsenceSeeder extends Seeder
{
    public function run()
    {
        Absence::create([
            'eleve_id' => 1,
            'date_absence' => now()->subDays(2),
            'motif' => 'Maladie',
            'justifie' => true,
        ]);
    }
}
