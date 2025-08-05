<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    const CACHE_TTL = 3600; // 1 heure
    const CACHE_TTL_LONG = 86400; // 24 heures

    /**
     * Cache des produits populaires
     */
    public static function getProduitsPopulaires(int $limit = 8): array
    {
        return Cache::remember('produits_populaires_' . $limit, self::CACHE_TTL, function () use ($limit) {
            return \App\Models\Product::with('categorie')
                ->actifs()
                ->withCount(['articlesCommande' => function ($query) {
                    $query->whereHas('commande', function ($q) {
                        $q->where('statut', 'livree');
                    });
                }])
                ->orderBy('articles_commande_count', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache des catégories avec compteurs
     */
    public static function getCategories(): array
    {
        return Cache::remember('categories_avec_compteurs', self::CACHE_TTL_LONG, function () {
            return \App\Models\Category::with('enfants')
                ->actives()
                ->principales()
                ->ordonnees()
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache des statistiques du dashboard
     */
    public static function getStatistiquesDashboard(): array
    {
        return Cache::remember('stats_dashboard', 300, function () { // 5 minutes
            $maintenant = now();
            $debutMois = $maintenant->startOfMonth()->copy();
            $finMois = $maintenant->endOfMonth()->copy();

            return [
                'commandes_mois' => \App\Models\Order::whereBetween('created_at', [$debutMois, $finMois])->count(),
                'ca_mois' => \App\Models\Order::whereBetween('created_at', [$debutMois, $finMois])
                    ->where('statut_paiement', 'paye')
                    ->sum('total'),
                'nouveaux_clients' => \App\Models\User::whereBetween('created_at', [$debutMois, $finMois])
                    ->where('role', 'client')
                    ->count(),
                'produits_stock_faible' => \App\Models\Product::where('stock', '<=', 5)
                    ->actifs()
                    ->count(),
                'avis_en_attente' => \App\Models\Review::where('modere', false)
                    ->actifs()
                    ->count()
            ];
        });
    }

    /**
     * Cache des filtres de produits
     */
    public static function getFiltresProduits(): array
    {
        return Cache::remember('filtres_produits', self::CACHE_TTL, function () {
            return [
                'categories' => \App\Models\Category::actives()->ordonnees()->get(['id', 'nom'])->toArray(),
                'marques' => \App\Models\Product::distinct()->pluck('marque')->filter()->sort()->values()->toArray(),
                'prix_min' => \App\Models\Product::actifs()->min('prix'),
                'prix_max' => \App\Models\Product::actifs()->max('prix'),
                'notes' => [1, 2, 3, 4, 5]
            ];
        });
    }

    /**
     * Invalider le cache des produits
     */
    public static function invalidateProduitsCache(): void
    {
        Cache::forget('produits_populaires_8');
        Cache::forget('filtres_produits');

        // Invalider tous les caches de produits populaires
        for ($i = 1; $i <= 20; $i++) {
            Cache::forget('produits_populaires_' . $i);
        }
    }

    /**
     * Invalider le cache des catégories
     */
    public static function invalidateCategoriesCache(): void
    {
        Cache::forget('categories_avec_compteurs');
        Cache::forget('filtres_produits');
    }

    /**
     * Invalider le cache des statistiques
     */
    public static function invalidateStatsCache(): void
    {
        Cache::forget('stats_dashboard');
    }

    /**
     * Cache des recommandations pour un utilisateur
     */
    public static function getRecommandations(int $userId, int $limit = 6): array
    {
        return Cache::remember("recommandations_user_{$userId}_{$limit}", self::CACHE_TTL, function () use ($userId, $limit) {
            // Logique de recommandation basée sur l'historique d'achat
            $categoriesAchetees = \App\Models\OrderItem::whereHas('commande', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('statut', 'livree');
            })
                ->with('produit.categorie')
                ->get()
                ->pluck('produit.categorie.id')
                ->unique()
                ->filter();

            if ($categoriesAchetees->isEmpty()) {
                // Si pas d'historique, retourner les produits populaires
                return self::getProduitsPopulaires($limit);
            }

            return \App\Models\Product::with('categorie')
                ->actifs()
                ->whereIn('category_id', $categoriesAchetees)
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('product_id')
                        ->from('order_items')
                        ->whereHas('commande', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        });
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache des produits similaires
     */
    public static function getProduitsSimilaires(int $productId, int $limit = 4): array
    {
        return Cache::remember("similaires_produit_{$productId}_{$limit}", self::CACHE_TTL, function () use ($productId, $limit) {
            $produit = \App\Models\Product::find($productId);

            if (!$produit) {
                return [];
            }

            return \App\Models\Product::with('categorie')
                ->actifs()
                ->where('category_id', $produit->category_id)
                ->where('id', '!=', $productId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Nettoyer tout le cache
     */
    public static function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Obtenir les statistiques du cache Redis
     */
    public static function getCacheStats(): array
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();

            return [
                'connected_clients' => $info['connected_clients'] ?? 0,
                'used_memory_human' => $info['used_memory_human'] ?? '0B',
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $info['keyspace_hits'] > 0 ?
                    round(($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses'])) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Redis non disponible'
            ];
        }
    }
}
