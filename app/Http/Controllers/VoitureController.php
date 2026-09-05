<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VoitureController extends Controller
{
    /**
     * Affiche la liste des voitures.
     */
    public function index(): View
    {
        $voitures = Voiture::query()
            ->orderBy('marque')
            ->orderBy('modele')
            ->get();

        return view('voitures.index', compact('voitures'));
    }

    /**
     * Crée ou modifie une voiture.
     */
    public function store(Request $request): JsonResponse
    {
        $isUpdate = $request->filled('id');

        $data = $request->validate([
            'id' => [
                'nullable',
                'integer',
                'exists:voitures,id',
            ],

            'immatriculation' => [
                'required',
                'string',
                'max:255',
                Rule::unique('voitures', 'immatriculation')
                    ->ignore($request->input('id')),
            ],

            'marque' => [
                'required',
                'string',
                'max:255',
            ],

            'modele' => [
                'required',
                'string',
                'max:255',
            ],

            'kilometrage' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'statut' => [
                'required',
                Rule::in([
                    'disponible',
                    'réservé',
                    'indisponible',
                ]),
            ],
        ]);

        $voiture = $isUpdate
            ? Voiture::findOrFail($data['id'])
            : new Voiture();

        $voiture->immatriculation = strtoupper(
            trim($data['immatriculation'])
        );

        $voiture->marque = trim($data['marque']);
        $voiture->modele = trim($data['modele']);
        $voiture->kilometrage = $data['kilometrage'] ?? 0;
        $voiture->statut = $data['statut'];

        $voiture->save();

        return response()->json([
            'success' => true,
            'message' => $isUpdate
                ? 'La voiture a été modifiée.'
                : 'La voiture a été créée.',
            'voiture' => $voiture->fresh(),
        ]);
    }
}