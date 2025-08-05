<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id'
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
     * Ajouter un produit à la wishlist
     */
    public static function ajouterProduit(int $userId, int $productId): bool
    {
        try {
            self::firstOrCreate([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retirer un produit de la wishlist
     */
    public static function retirerProduit(int $userId, int $productId): bool
    {
        return self::where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete() > 0;
    }

    /**
     * Vérifier si un produit est dans la wishlist
     */
    public static function estDansWishlist(int $userId, int $productId): bool
    {
        return self::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
}
