<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'numero_commande' => $this->numero_commande,
            'statut' => $this->statut,
            'statut_libelle' => $this->statut_libelle,
            'statut_paiement' => $this->statut_paiement,
            'statut_paiement_libelle' => $this->statut_paiement_libelle,
            'mode_paiement' => $this->mode_paiement,
            'sous_total' => $this->sous_total,
            'frais_livraison' => $this->frais_livraison,
            'taxes' => $this->taxes,
            'remise' => $this->remise,
            'total' => $this->total,
            'livraison' => [
                'nom' => $this->nom_livraison,
                'prenom' => $this->prenom_livraison,
                'adresse' => $this->adresse_livraison,
                'ville' => $this->ville_livraison,
                'code_postal' => $this->code_postal_livraison,
                'pays' => $this->pays_livraison,
                'telephone' => $this->telephone_livraison,
                'adresse_complete' => $this->adresse_livraison_complete,
            ],
            'facturation' => $this->when($this->nom_facturation, [
                'nom' => $this->nom_facturation,
                'prenom' => $this->prenom_facturation,
                'adresse' => $this->adresse_facturation,
                'ville' => $this->ville_facturation,
                'code_postal' => $this->code_postal_facturation,
                'pays' => $this->pays_facturation,
            ]),
            'notes_client' => $this->notes_client,
            'notes_admin' => $this->when($request->user()?->estAdmin(), $this->notes_admin),
            'code_promo' => $this->code_promo,
            'utilisateur' => new UserResource($this->whenLoaded('utilisateur')),
            'articles' => OrderItemResource::collection($this->whenLoaded('articles')),
            'dates' => [
                'commande' => $this->created_at?->format('Y-m-d H:i:s'),
                'paiement' => $this->date_paiement?->format('Y-m-d H:i:s'),
                'expedition' => $this->date_expedition?->format('Y-m-d H:i:s'),
                'livraison' => $this->date_livraison?->format('Y-m-d H:i:s'),
            ],
        ];
    }
}
