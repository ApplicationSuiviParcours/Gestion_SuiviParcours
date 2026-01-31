<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EnseignantMatiereClasse;
use App\Models\EmploiDuTemps;
use App\Models\Classe;

class Matiere extends Model
{
    use HasFactory;

    protected $fillable = ['libelle','coefficient'];

    /**
     * Relation vers toutes les classes qui utilisent cette matière
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            Classe::class,               // modèle lié
            'enseignant_matiere_classe', // table pivot
            'matiere_id',                // clé étrangère de Matiere dans pivot
            'classe_id'                  // clé étrangère de Classe dans pivot
        )->withPivot('coefficient')->withTimestamps();            // si pivot a created_at et updated_at
    }

    public function affectations(): HasMany
    {
        return $this->hasMany(EnseignantMatiereClasse::class);
    }

    public function emploisDuTemps(): HasMany 
    {
        return $this->hasMany(EmploiDuTemps::class);
    }

    



}