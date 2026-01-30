<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('bulletin_id')
                  ->constrained('bulletins')
                  ->cascadeOnDelete();

            $table->foreignId('evaluation_id')
                  ->constrained('evaluations')
                  ->cascadeOnDelete();

            $table->foreignId('eleve_id')
                  ->constrained('eleves')
                  ->cascadeOnDelete();

            $table->foreignId('matiere_id')
                  ->constrained('matieres')
                  ->cascadeOnDelete();

            // Valeur de la note et coefficient
            $table->decimal('valeur', 5, 2);
            $table->decimal('coefficient', 5, 2);

            $table->timestamps();

            // Une note unique par élève / évaluation / bulletin
            $table->unique(['bulletin_id', 'eleve_id', 'evaluation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
