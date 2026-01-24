<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Classe;

class EnseignantMatiereClasse extends Model
{
    use HasFactory;

    protected $table = 'enseignant_matiere_classe';

    protected $fillable = [
        'enseignant_id',
        'matiere_id',
        'classe_id',
    ];

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

}
