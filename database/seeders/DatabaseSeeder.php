<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            ClasseSeeder::class,
            AnneeScolaireSeeder::class,
            EleveSeeder::class,
            ParentEleveSeeder::class,
            InscriptionSeeder::class,
            MatiereSeeder::class,
            EnseignantSeeder::class,
            EnseignantMatiereClasseSeeder::class,
            EvaluationSeeder::class,
            NoteSeeder::class,
            AbsenceSeeder::class,
            BulletinSeeder::class,
            EmploiDuTempsSeeder::class,
            ParametreSeeder::class,
            ClasseMatiereSeeder::class,
        ]);
    }
}
