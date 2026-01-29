<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('emploi_du_temps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('classe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enseignant_id')->constrained('users')->cascadeOnDelete();

            $table->string('jour'); // Lundi, Mardi...
            $table->time('heure_debut');
            $table->time('heure_fin');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emploi_du_temps');
    }
};

    