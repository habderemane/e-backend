<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero_commande', 'user_id', 'statut', 'statut_paiement',
        'mode_paiement', 'sous_total', 'frais_livraison', 'taxes',
        'remise', 'total', 'nom_livraison', 'prenom_livraison',
        'adresse_livraison', 'ville_livraison', 'code_postal_livraison',
        'pays_livraison', 'telephone_livraison', 'notes_client'
    ];

    protected $casts = [
        'sous_total' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'taxes' => 'decimal:2',
        'remise' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relations
    public function utilisateur() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function articles() {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Méthodes utilitaires
    public static function genererNumeroCommande(): string {
        do {
            $numero = 'CMD-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('numero_commande', $numero)->exists());

        return $numero;
    }
}
