<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'nom' => $this->nom,
            'description' => $this->description,
            'slug' => $this->slug,
            'image' => $this->url_image,
            'actif' => $this->actif,
            'ordre' => $this->ordre,
            'nombre_produits' => $this->nombre_produits,
            'parent_id' => $this->parent_id,
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'enfants' => CategoryResource::collection($this->whenLoaded('enfants')),
            'chemin_complet' => $this->chemin_complet,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'date_creation' => $this->created_at?->format('Y-m-d H:i:s'),
            'date_modification' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
