<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'note_etoiles' => $this->note_etoiles,
            'titre' => $this->titre,
            'commentaire' => $this->commentaire,
            'images' => $this->images,
            'recommande' => $this->recommande,
            'verifie' => $this->verifie,
            'utile_count' => $this->utile_count,
            'temps_ecoule' => $this->temps_ecoule,
            'utilisateur' => [
                'id' => $this->utilisateur->id,
                'nom' => $this->utilisateur->nom,
                'prenom' => $this->utilisateur->prenom,
                'avatar_url' => $this->utilisateur->avatar_url ?? null,
            ],
            'produit' => new ProductResource($this->whenLoaded('produit')),
            'date_creation' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
