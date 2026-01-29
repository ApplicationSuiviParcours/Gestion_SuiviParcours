<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AnneeScolaire;
use App\Models\Eleve;

class Bulletin extends Model
{
    use HasFactory;

    protected $fillable = ['eleve_id', 'annee_id', 'moyenne_generale', 'appreciation'];

    public function eleve(): BelongsTo {
        return $this->belongsTo(Eleve::class);
    }

    public function annee(): BelongsTo {
        return $this->belongsTo(AnneeScolaire::class);
    }
}
