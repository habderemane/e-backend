<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('numero_commande')->unique();
            $table->unsignedBigInteger('user_id');
            $table->enum('statut', ['en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee'])
                ->default('en_attente');
            $table->enum('statut_paiement', ['en_attente', 'paye', 'rembourse', 'echec'])
                ->default('en_attente');
            $table->enum('mode_paiement', ['avant_livraison', 'apres_livraison', 'carte_bancaire'])
                ->default('avant_livraison');

            // Montants
            $table->decimal('sous_total', 10, 2);
            $table->decimal('frais_livraison', 10, 2)->default(0);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('remise', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Informations de livraison
            $table->string('nom_livraison');
            $table->string('prenom_livraison');
            $table->text('adresse_livraison');
            $table->string('ville_livraison');
            $table->string('code_postal_livraison');
            $table->string('pays_livraison')->default('France');
            $table->string('telephone_livraison')->nullable();

            // Métadonnées
            $table->text('notes_client')->nullable();
            $table->text('notes_admin')->nullable();
            $table->timestamp('date_paiement')->nullable();
            $table->timestamp('date_expedition')->nullable();
            $table->timestamp('date_livraison')->nullable();

            $table->timestamps();

            // Clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index pour performance
            $table->index(['user_id', 'statut']);
            $table->index('numero_commande');
            $table->index('statut');
            $table->index('statut_paiement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
