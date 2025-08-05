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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_id')->nullable(); // Vérifier que l'utilisateur a acheté le produit
            $table->integer('note')->unsigned()->comment('Note de 1 à 5');
            $table->string('titre')->nullable();
            $table->text('commentaire')->nullable();
            $table->json('images')->nullable(); // Images jointes par l'utilisateur
            $table->boolean('recommande')->default(true);
            $table->boolean('verifie')->default(false); // Avis vérifié (achat confirmé)
            $table->boolean('modere')->default(false); // Modéré par un admin
            $table->boolean('actif')->default(true);
            $table->integer('utile_count')->default(0); // Nombre de "utile"
            $table->timestamps();

            // Clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            // Contrainte unique : un utilisateur ne peut donner qu'un avis par produit
            $table->unique(['user_id', 'product_id']);

            // Index pour performance
            $table->index(['product_id', 'actif', 'modere']);
            $table->index(['note', 'actif']);
            $table->index(['created_at', 'actif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
