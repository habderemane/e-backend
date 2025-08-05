<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'nom_produit',
        'description_produit',
        'prix_unitaire',
        'quantite',
        'total_ligne',
        'sku_produit',
        'image_produit'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'total_ligne' => 'decimal:2',
        'quantite' => 'integer',
        'image_produit' => 'array'
    ];

    /**
     * Relation avec la commande
     */
    public function commande()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relation avec le produit
     */
    public function produit()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Calculer le total de la ligne
     */
    public function calculerTotal(): void
    {
        $this->total_ligne = $this->prix_unitaire * $this->quantite;
        $this->save();
    }

    /**
     * Boot method pour calculer automatiquement le total
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            $orderItem->total_ligne = $orderItem->prix_unitaire * $orderItem->quantite;
        });
    }
}
