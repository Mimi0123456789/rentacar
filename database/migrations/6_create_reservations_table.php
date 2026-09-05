<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('voiture_id')
                ->nullable()
                ->constrained('voitures')
                ->nullOnDelete();
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->string('motif')->nullable();
            $table->unsignedInteger('nb_passagers')->default(1);
            $table->boolean('bagages')->default(false);
            $table->string('statut')->default('en attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};