<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Matiere;
use App\Models\Classe;

class ClasseMatiere extends Model
{
    use HasFactory;

    protected $table = 'classe_matiere';

    protected $fillable = [
        'classe_id',
        'matiere_id',
        'coefficient',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }
}
