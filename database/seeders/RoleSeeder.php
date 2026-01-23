<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $permissions = [
            'gerer utilisateurs',
            'gerer eleves',
            'gerer inscriptions',
            'gerer classes',
            'saisir notes',
            'gerer evaluations',
            'consulter notes',
            'consulter parcours',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rôles
        $admin = Role::firstOrCreate(['name' => 'Administrateur']);
        $scolarite = Role::firstOrCreate(['name' => 'Scolarite']);
        $enseignant = Role::firstOrCreate(['name' => 'Enseignant']);
        $eleve = Role::firstOrCreate(['name' => 'Eleve']);
        $parent = Role::firstOrCreate(['name' => 'Parent']);

        // Attribution des permissions
        $admin->givePermissionTo(Permission::all());

        $scolarite->givePermissionTo([
            'gerer eleves',
            'gerer inscriptions',
            'gerer classes',
        ]);

        $enseignant->givePermissionTo([
            'saisir notes',
            'gerer evaluations',
        ]);

        $eleve->givePermissionTo([
            'consulter notes',
            'consulter parcours',
        ]);

        $parent->givePermissionTo([
            'consulter parcours',
        ]);
    }
}

