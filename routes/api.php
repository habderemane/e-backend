<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// Routes publiques pour les produits et catégories
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('products/{id}/reviews', [ReviewController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

// Routes protégées par authentification JWT
Route::middleware('auth:api')->group(function () {
    
    // Authentification
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
    
    // Profil utilisateur
    Route::prefix('users')->group(function () {
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile', [UserController::class, 'updateProfile']);
        Route::post('avatar', [UserController::class, 'uploadAvatar']);
    });
    
    // Commandes
    Route::prefix('orders')->group(function () {
        Route::get('my-orders', [OrderController::class, 'myOrders']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('{id}', [OrderController::class, 'show']);
        Route::get('{id}/invoice', [OrderController::class, 'downloadInvoice']);
    });
    
    // Wishlist
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('{productId}', [WishlistController::class, 'store']);
        Route::delete('{productId}', [WishlistController::class, 'destroy']);
        Route::get('{productId}/check', [WishlistController::class, 'check']);
        Route::delete('/', [WishlistController::class, 'clear']);
    });
    
    // Reviews
    Route::prefix('products/{productId}/reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store']);
    });
    
    Route::prefix('reviews')->group(function () {
        Route::post('{reviewId}/helpful', [ReviewController::class, 'markAsHelpful']);
    });
    
    // Routes administrateur
    Route::middleware('admin')->group(function () {
        
        // Gestion des utilisateurs
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('{id}', [UserController::class, 'show']);
            Route::put('{id}', [UserController::class, 'update']);
            Route::delete('{id}', [UserController::class, 'destroy']);
            Route::put('{id}/toggle-status', [UserController::class, 'toggleStatus']);
        });
        
        // Gestion des produits
        Route::prefix('products')->group(function () {
            Route::post('/', [ProductController::class, 'store']);
            Route::put('{id}', [ProductController::class, 'update']);
            Route::delete('{id}', [ProductController::class, 'destroy']);
            Route::post('{id}/images', [ProductController::class, 'uploadImages']);
            Route::delete('{id}/images/{imageIndex}', [ProductController::class, 'deleteImage']);
            Route::put('{id}/toggle-status', [ProductController::class, 'toggleStatus']);
            Route::put('{id}/stock', [ProductController::class, 'updateStock']);
        });
        
        // Gestion des catégories
        Route::prefix('categories')->group(function () {
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('{id}', [CategoryController::class, 'update']);
            Route::delete('{id}', [CategoryController::class, 'destroy']);
            Route::post('{id}/image', [CategoryController::class, 'uploadImage']);
            Route::put('{id}/toggle-status', [CategoryController::class, 'toggleStatus']);
        });
        
        // Gestion des commandes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::put('{id}/status', [OrderController::class, 'updateStatus']);
            Route::put('{id}/payment', [OrderController::class, 'updatePaymentStatus']);
        });
        
        // Gestion des avis (admin)
        Route::prefix('reviews')->group(function () {
            Route::put('{reviewId}/moderate', [ReviewController::class, 'moderate']);
        });
        
        // Statistiques et tableaux de bord
        Route::prefix('dashboard')->group(function () {
            Route::get('stats', [OrderController::class, 'dashboardStats']);
            Route::get('recent-orders', [OrderController::class, 'recentOrders']);
            Route::get('top-products', [ProductController::class, 'topProducts']);
            Route::get('low-stock', [ProductController::class, 'lowStock']);
        });
    });
});

// Route de test pour vérifier que l'API fonctionne
Route::get('health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'API Laravel E-commerce fonctionne correctement',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'database' => 'PostgreSQL',
        'framework' => 'Laravel ' . app()->version()
    ]);
});

// Route pour obtenir la configuration publique
Route::get('config', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'pagination_per_page' => config('app.pagination_per_page', 12),
        'max_file_size' => config('app.max_file_size', 10240),
        'supported_image_types' => ['jpg', 'jpeg', 'png', 'webp'],
        'currency' => 'EUR',
        'currency_symbol' => '€',
        'timezone' => config('app.timezone'),
        'locale' => 'fr'
    ]);
});