<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'nom', 'description', 'prix', 'stock', 'category_id',
        'images', 'actif', 'poids', 'dimensions', 'marque', 'sku',
        'prix_promo', 'date_debut_promo', 'date_fin_promo', 'slug'
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'prix_promo' => 'decimal:2',
        'stock' => 'integer',
        'actif' => 'boolean',
        'images' => 'array',
        'dimensions' => 'array',
        'date_debut_promo' => 'datetime',
        'date_fin_promo' => 'datetime',
    ];

    // Relations
    public function categorie() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Accesseurs
    public function getPrixFinalAttribute(): float {
        if ($this->en_promotion) {
            return (float) $this->prix_promo;
        }
        return (float) $this->prix;
    }

    public function getEnPromotionAttribute(): bool {
        if (!$this->prix_promo || !$this->date_debut_promo || !$this->date_fin_promo) {
            return false;
        }
        $maintenant = now();
        return $maintenant->between($this->date_debut_promo, $this->date_fin_promo);
    }

    // Scopes
    public function scopeActifs($query) {
        return $query->where('actif', true);
    }

    public function scopeEnStock($query) {
        return $query->where('stock', '>', 0);
    }
}
