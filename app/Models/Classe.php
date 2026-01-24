<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\EnseignantMatiereClasse;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_classe',
        'niveau',
        'filiere',
        'effectif_max',
    ];

    /**
     * Relation vers les inscriptions de cette classe
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'classe_id', 'id');
    }

    /**
     * Relation vers tous les élèves de cette classe via les inscriptions
     */
    public function eleves(): HasManyThrough
    {
        return $this->hasManyThrough(
            Eleve::class,       // modèle final
            Inscription::class, // modèle intermédiaire
            'classe_id',        // clé étrangère dans inscriptions vers classes
            'id',               // clé primaire dans eleves
            'id',               // clé primaire dans classes
            'eleve_id'          // clé étrangère dans inscriptions vers eleves
        );
    }

    public function affectations(): HasMany
    {
        return $this->hasMany(EnseignantMatiereClasse::class);
    }

}