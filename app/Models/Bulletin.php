<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\AnneeScolaire;
use App\Models\Note;

class Bulletin extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_id',
        'periode',
        'moyenne',
        'rang',
        'appreciation',
    ];

    public function eleve(): BelongsTo {
        return $this->belongsTo(Eleve::class);
    }

    public function classe(): BelongsTo {
        return $this->belongsTo(Classe::class);
    }

    public function notes(): HasMany {
        return $this->hasMany(Note::class);
    }

    public function annee(): BelongsTo 
    { 
        return $this->belongsTo(AnneeScolaire::class); 
    }
}

 
