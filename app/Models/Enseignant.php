<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Matiere;
use App\Models\Classe;

class Enseignant extends Model
{
    use HasFactory;

     protected $fillable = [
        'nom',
        'prenom',
        'specialite',
        'telephone',
        'email',
    ];

    /**
     * Un enseignant peut enseigner plusieurs matières
     * dans plusieurs classes
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(
            Matiere::class,
            'enseignant_matiere_classe'
        )->withPivot('classe_id')->withTimestamps();
    }

    /**
     * Classes dans lesquelles l’enseignant intervient
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            Classe::class,
            'enseignant_matiere_classe'
        )->withPivot('matiere_id')->withTimestamps();
    }
}