<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'prenom' => $this->prenom,
            'nom_complet' => $this->nom_complet,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'pays' => $this->pays,
            'date_naissance' => $this->date_naissance?->format('Y-m-d'),
            'role' => $this->role,
            'avatar_url' => $this->avatar_url,
            'actif' => $this->actif,
            'email_verifie' => !is_null($this->email_verifie_le),
            'email_verifie_le' => $this->email_verifie_le?->format('Y-m-d H:i:s'),
            'membre_depuis' => $this->created_at?->format('Y-m-d'),
            'derniere_connexion' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
