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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();

        $table->foreignId('eleve_id')
            ->constrained('eleves')
            ->cascadeOnDelete();

        $table->foreignId('classe_id')
            ->constrained('classes')
            ->cascadeOnDelete();

        $table->foreignId('annee_id')
            ->constrained('annees_scolaires')
            ->cascadeOnDelete();

        $table->string('periode'); // Trimestre 1, 2, 3
        $table->decimal('moyenne', 5, 2)->nullable();
        $table->integer('rang')->nullable();
        $table->text('appreciation')->nullable();

        $table->timestamps();

        $table->unique(['eleve_id', 'classe_id', 'annee_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
