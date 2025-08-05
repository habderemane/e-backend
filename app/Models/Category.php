<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'nom', 'description', 'image', 'actif',
        'parent_id', 'ordre', 'slug'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];

    // Relations
    public function produits() {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function enfants() {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Scopes
    public function scopeActives($query) {
        return $query->where('actif', true);
    }

    public function scopePrincipales($query) {
        return $query->whereNull('parent_id');
    }
}
