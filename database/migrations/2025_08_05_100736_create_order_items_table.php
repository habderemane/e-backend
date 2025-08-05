<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');

            // Informations du produit au moment de la commande
            $table->string('nom_produit');
            $table->text('description_produit')->nullable();
            $table->decimal('prix_unitaire', 10, 2);
            $table->integer('quantite');
            $table->decimal('total_ligne', 10, 2);
            $table->string('sku_produit')->nullable();
            $table->json('image_produit')->nullable();

            $table->timestamps();

            // Clés étrangères
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Index
            $table->index(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
