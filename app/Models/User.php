<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $fillable = [
        'nom', 'prenom', 'email', 'mot_de_passe',
        'telephone', 'adresse', 'ville', 'code_postal',
        'pays', 'role', 'date_naissance', 'actif'
    ];

    protected $hidden = ['mot_de_passe', 'remember_token'];

    protected $casts = [
        'email_verifie_le' => 'datetime',
        'date_naissance' => 'date',
        'actif' => 'boolean',
    ];

    // Méthodes JWT
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [
            'role' => $this->role,
            'nom' => $this->nom,
            'prenom' => $this->prenom
        ];
    }

    // Méthodes utilitaires
    public function estAdmin(): bool {
        return $this->role === 'admin';
    }

    public function commandes() {
        return $this->hasMany(Order::class, 'user_id');
    }
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}
