<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Eleve;

class ParentEleve extends Model
{
    use HasFactory;

    // Table réelle
    protected $table = 'parents';

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
    ];

    /**
     * Tous les élèves liés à ce parent
     */
    public function eleves(): BelongsToMany
    {
        return $this->belongsToMany(
            Eleve::class,     // modèle lié
            'eleve_parent',   // table pivot
            'parent_id',      // clé étrangère dans pivot pour Parent
            'eleve_id'        // clé étrangère dans pivot pour Eleve
        )->withPivot('lien')->withTimestamps();
    }
}