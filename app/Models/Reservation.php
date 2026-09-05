<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    public const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    public const STATUT_VALIDEE = 'VALIDEE';
    public const STATUT_REFUSEE = 'REFUSEE';
    public const STATUT_ANNULEE = 'ANNULEE';
    public const STATUT_TERMINEE = 'TERMINEE';

    protected $fillable = [
        'user_id',
        'voiture_id',
        'date_debut',
        'date_fin',
        'kilometrage_depart',
        'kilometrage_retour',
        'motif',
        'nb_passagers',
        'bagages',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'kilometrage_depart' => 'integer',
        'kilometrage_retour' => 'integer',
        'nb_passagers' => 'integer',
        'bagages' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voiture(): BelongsTo
    {
        return $this->belongsTo(Voiture::class);
    }
}