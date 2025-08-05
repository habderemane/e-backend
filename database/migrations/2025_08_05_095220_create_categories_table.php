<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Clé étrangère pour hiérarchie
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');

            // Index pour performance
            $table->index(['actif', 'parent_id']);
            $table->index('slug');
            $table->index('ordre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
