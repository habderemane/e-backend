<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product_id' => $this->product_id,
            'nom_produit' => $this->nom_produit,
            'description_produit' => $this->description_produit,
            'prix_unitaire' => $this->prix_unitaire,
            'quantite' => $this->quantite,
            'total_ligne' => $this->total_ligne,
            'sku_produit' => $this->sku_produit,
            'image_produit' => $this->image_produit,
            'produit' => new ProductResource($this->whenLoaded('produit')),
        ];
    }
}
