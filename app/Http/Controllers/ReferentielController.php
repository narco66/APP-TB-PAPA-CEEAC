<?php

namespace App\Http\Controllers;

use App\Models\Referentiel;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ReferentielController extends Controller
{
    const TYPES = [
        'categorie_action'   => 'Catégories d\'actions',
        'type_indicateur'    => 'Types d\'indicateurs',
        'unite_mesure'       => 'Unités de mesure',
        'frequence_collecte' => 'Fréquences de collecte',
        'source_financement' => 'Sources de financement',
        'type_document'      => 'Types de documents GED',
        'periode_reporting'  => 'Périodes de reporting',
        'niveau_priorite'    => 'Niveaux de priorité',
        'type_risque'        => 'Types de risques',
        'categorie_alerte'   => 'Catégories d\'alertes',
        'niveau_criticite'   => 'Niveaux de criticité',
        'type_objectif'      => 'Types d\'objectifs',
        'type_resultat'      => 'Types de résultats',
        'source_donnees'     => 'Sources de données',
        'type_notification'  => 'Types de notifications',
        'type_dependance'    => 'Types de dépendances',
        'type_commentaire'   => 'Types de commentaires',
    ];

    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $this->authorize('parametres.referentiels.voir');

        $stats = collect(self::TYPES)->map(fn($libelle, $type) => [
            'type'    => $type,
            'libelle' => $libelle,
            'total'   => Referentiel::where('type', $type)->count(),
            'actifs'  => Referentiel::where('type', $type)->where('actif', true)->count(),
        ]);

        return view('parametres.referentiels.index', compact('stats'));
    }

    public function liste(string $type)
    {
        $this->authorize('parametres.referentiels.voir');
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $referentiels = Referentiel::where('type', $type)
            ->orderBy('ordre')
            ->orderBy('libelle')
            ->get();

        $libelleType = self::TYPES[$type];

        return view('parametres.referentiels.liste', compact('referentiels', 'type', 'libelleType'));
    }

    public function store(Request $request, string $type)
    {
        $this->authorize('parametres.referentiels.gerer');
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $data = $request->validate([
            'code'          => 'required|string|max:40',
            'libelle'       => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:60',
            'description'   => 'nullable|string|max:500',
            'couleur'       => 'nullable|string|max:20',
            'ordre'         => 'nullable|integer|min:0',
        ]);

        $exists = Referentiel::where('type', $type)->where('code', strtoupper($data['code']))->exists();
        abort_if($exists, 422, "Le code {$data['code']} existe déjà pour ce référentiel.");

        $data['code'] = strtoupper($data['code']);

        $referentiel = Referentiel::create(array_merge($data, [
            'type'     => $type,
            'actif'    => true,
            'cree_par' => $request->user()->id,
        ]));

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'referentiel_cree',
            auditable: $referentiel,
            acteur: $request->user(),
            action: 'creer',
            description: "Référentiel {$type}/{$referentiel->code} créé",
        );

        return back()->with('success', "Entrée \"{$referentiel->libelle}\" ajoutée.");
    }

    public function update(Request $request, string $type, Referentiel $referentiel)
    {
        $this->authorize('parametres.referentiels.gerer');
        abort_if($referentiel->est_systeme, 403, 'Ce référentiel système ne peut pas être modifié.');

        $data = $request->validate([
            'libelle'       => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:60',
            'description'   => 'nullable|string|max:500',
            'couleur'       => 'nullable|string|max:20',
            'ordre'         => 'nullable|integer|min:0',
        ]);

        $avant = $referentiel->toArray();
        $referentiel->update(array_merge($data, ['modifie_par' => $request->user()->id]));

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'referentiel_modifie',
            auditable: $referentiel,
            acteur: $request->user(),
            action: 'modifier',
            description: "Référentiel {$type}/{$referentiel->code} modifié",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Référentiel \"{$referentiel->libelle}\" mis à jour.");
    }

    public function toggle(Request $request, string $type, Referentiel $referentiel)
    {
        $this->authorize('parametres.referentiels.gerer');
        abort_if($referentiel->est_systeme && $referentiel->actif, 403, 'Ce référentiel système actif ne peut pas être désactivé.');

        $referentiel->update(['actif' => !$referentiel->actif]);
        $etat = $referentiel->actif ? 'activé' : 'désactivé';

        return back()->with('success', "\"{$referentiel->libelle}\" {$etat}.");
    }

    public function destroy(Request $request, string $type, Referentiel $referentiel)
    {
        $this->authorize('parametres.referentiels.gerer');
        abort_if($referentiel->est_systeme, 403, 'Ce référentiel système ne peut pas être supprimé. Désactivez-le à la place.');

        $referentiel->delete();

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'referentiel_supprime',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'supprimer',
            description: "Référentiel {$type}/{$referentiel->code} supprimé",
        );

        return back()->with('success', "Référentiel \"{$referentiel->libelle}\" supprimé.");
    }

    public function reordonner(Request $request, string $type)
    {
        $this->authorize('parametres.referentiels.gerer');
        $request->validate(['ordre' => 'required|array', 'ordre.*' => 'integer']);

        foreach ($request->ordre as $position => $id) {
            Referentiel::where('id', $id)->where('type', $type)->update(['ordre' => $position]);
        }

        return response()->json(['ok' => true]);
    }
}
