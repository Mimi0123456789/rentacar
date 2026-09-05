<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voiture extends Model
{
    use HasFactory;

    /**
     * Nom de la table.
     */
    protected $table = 'voitures';

    /**
     * Attributs pouvant être remplis en masse.
     */
    protected $fillable = [
        'immatriculation',
        'marque',
        'modele',
        'kilometrage',
        'statut',
    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [
        'kilometrage' => 'integer',
        'statut' => 'string',
    ];

    /**
     * Relation avec les locations.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}