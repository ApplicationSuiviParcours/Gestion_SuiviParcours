<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Note;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\AnneeScolaire;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_evaluation',
        'date_evaluation',
        'classe_id',
        'matiere_id',
        'annee_id',
    ];

    /**
     * Relation vers les notes de cette évaluation
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'evaluation_id', 'id');
    }

    /**
     * Relation vers la classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id', 'id');
    }

    /**
     * Relation vers la matière
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id', 'id');
    }

    /**
     * Relation vers l'année scolaire
     */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_id', 'id');
    }
}