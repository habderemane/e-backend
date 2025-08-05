<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->timestamp('email_verifie_le')->nullable();
            $table->string('mot_de_passe');
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->default('France');
            $table->date('date_naissance')->nullable();
            $table->enum('role', ['client', 'admin'])->default('client');
            $table->string('avatar')->nullable();
            $table->boolean('actif')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // Index pour performance
            $table->index(['email', 'actif']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
