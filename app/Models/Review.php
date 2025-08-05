<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'note',
        'titre',
        'commentaire',
        'images',
        'recommande',
        'verifie',
        'modere',
        'actif',
        'utile_count'
    ];

    protected $casts = [
        'note' => 'integer',
        'images' => 'array',
        'recommande' => 'boolean',
        'verifie' => 'boolean',
        'modere' => 'boolean',
        'actif' => 'boolean',
        'utile_count' => 'integer',
    ];

    protected $appends = [
        'note_etoiles',
        'temps_ecoule'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec le produit
     */
    public function produit()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relation avec la commande
     */
    public function commande()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Obtenir la note sous forme d'étoiles
     */
    public function getNoteEtoilesAttribute(): string
    {
        return str_repeat('★', $this->note) . str_repeat('☆', 5 - $this->note);
    }

    /**
     * Obtenir le temps écoulé depuis la création
     */
    public function getTempsEcouleAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope pour les avis actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les avis modérés
     */
    public function scopeModeres($query)
    {
        return $query->where('modere', true);
    }

    /**
     * Scope pour les avis vérifiés (achat confirmé)
     */
    public function scopeVerifies($query)
    {
        return $query->where('verifie', true);
    }

    /**
     * Scope pour une note spécifique
     */
    public function scopeParNote($query, $note)
    {
        return $query->where('note', $note);
    }

    /**
     * Scope pour trier par utilité
     */
    public function scopeParUtilite($query)
    {
        return $query->orderBy('utile_count', 'desc');
    }

    /**
     * Scope pour trier par date
     */
    public function scopeRecents($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Vérifier si l'utilisateur peut laisser un avis
     */
    public static function peutLaisserAvis(int $userId, int $productId): bool
    {
        // Vérifier si l'utilisateur a acheté le produit
        $aAchete = OrderItem::whereHas('commande', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('statut', 'livree');
        })->where('product_id', $productId)->exists();

        if (!$aAchete) {
            return false;
        }

        // Vérifier si l'utilisateur n'a pas déjà laissé un avis
        $dejaAvis = self::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return !$dejaAvis;
    }

    /**
     * Calculer la note moyenne d'un produit
     */
    public static function noteMoyenneProduit(int $productId): array
    {
        $avis = self::where('product_id', $productId)
            ->actifs()
            ->moderes();

        $moyenne = $avis->avg('note') ?? 0;
        $total = $avis->count();

        // Répartition par étoiles
        $repartition = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $avis->where('note', $i)->count();
            $repartition[$i] = [
                'count' => $count,
                'pourcentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
            ];
        }

        return [
            'moyenne' => round($moyenne, 1),
            'total' => $total,
            'repartition' => $repartition
        ];
    }
}
