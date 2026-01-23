<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'date_absence',
        'motif',
        'justifie',
    ];

    /**
     * Relation vers l'élève concerné
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id', 'id');
        // 'eleve_id' = clé étrangère dans absences
        // 'id' = clé primaire dans eleves
    }
}