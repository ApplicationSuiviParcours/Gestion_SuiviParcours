<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Eleve;
use App\Models\Evaluation;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'eleve_id',
        'valeur',
    ];

    /**
     * Relation vers l'élève concerné par la note
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id', 'id');
    }

    /**
     * Relation vers l'évaluation correspondante
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id', 'id');
    }
}