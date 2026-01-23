<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Inscription;

class AnneeScolaire extends Model
{
    use HasFactory;
    
    protected $table = 'annees_scolaires';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'actif',
    ];

    /**
     * Relation vers toutes les inscriptions de cette année scolaire
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'annee_id', 'id');
    }
}
