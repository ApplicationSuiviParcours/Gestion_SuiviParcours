<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignant;

class EmploiDuTemps extends Model
{
    use HasFactory;

    protected $fillable = ['classe_id', 'jour', 'heure_debut', 'heure_fin', 'matiere_id', 'enseignant_id'];

    public function classe() {
        return $this->belongsTo(Classe::class);
    }

    public function matiere() {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant() {
        return $this->belongsTo(User::class, 'enseignant_id');
    }
}
