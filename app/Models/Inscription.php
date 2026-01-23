<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Eleve;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_id',
        'date_inscription',
        'statut',
    ];

    protected $dates = ['date_inscription'];

    /**
     * Relation vers l'élève inscrit
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id', 'id');
    }

    /**
     * Relation vers la classe de l'inscription
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id', 'id');
    }

    /**
     * Relation vers l'année scolaire de l'inscription
     */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_id', 'id');
    }
}