<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ParentEleve;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Bulletin;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'genre',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'telephone',
        'email',
        'photo',
        'statut',
    ];

    /**
     * Toutes les inscriptions de cet élève
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'eleve_id', 'id');
    }

    /**
     * Tous les parents liés à cet élève
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            ParentEleve::class,
            'eleve_parent', // table pivot
            'eleve_id',     // clé étrangère dans pivot pour Eleve
            'parent_id'     // clé étrangère dans pivot pour Parent
        )->withPivot('lien')->withTimestamps();
    }

    /**
     * Toutes les notes de cet élève
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'eleve_id', 'id');
    }

    /**
     * Toutes les absences de cet élève
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'eleve_id', 'id');
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

}