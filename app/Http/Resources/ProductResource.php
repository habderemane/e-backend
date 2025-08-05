<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'slug' => $this->slug,
            'prix' => $this->prix,
            'prix_promo' => $this->prix_promo,
            'prix_final' => $this->prix_final,
            'en_promotion' => $this->en_promotion,
            'stock' => $this->stock,
            'images' => $this->images,
            'marque' => $this->marque,
            'sku' => $this->sku,
            'actif' => $this->actif,
            'categorie' => new CategoryResource($this->whenLoaded('categorie')),
            'date_creation' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
