<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Les attributs pouvant être assignés massivement.
     */
    protected $fillable = [
        'name',
        'email',
        'login',
        'droit',
        'password',
    ];

    /**
     * Les attributs cachés lors de la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversion automatique des types.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Un utilisateur possède plusieurs locations.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->droit === 'ADMINISTRATEUR';
    }

    /**
     * Vérifie si l'utilisateur est gestionnaire.
     */
    public function isGestion(): bool
    {
        return $this->droit === 'GESTIONNAIRE';
    }

    /**
     * Vérifie si l'utilisateur est collaborateur.
     */
    public function isCollaborateur(): bool
    {
        return $this->droit === 'COLLABORATEUR';
    }
}