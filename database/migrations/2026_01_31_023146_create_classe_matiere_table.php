<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classe_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('coefficient');
            $table->timestamps();

            $table->unique(['classe_id', 'matiere_id']); // chaque matière ne peut apparaitre qu'une fois par classe
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classe_matiere');
    }
};
