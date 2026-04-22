<?php

namespace App\Http\Controllers;

use App\Models\Referentiel;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ReferentielController extends Controller
{
    public const TYPES = [
        'categorie_action' => 'Categories d actions',
        'type_indicateur' => 'Types d indicateurs',
        'unite_mesure' => 'Unites de mesure',
        'frequence_collecte' => 'Frequences de collecte',
        'source_financement' => 'Sources de financement',
        'type_document' => 'Types de documents GED',
        'periode_reporting' => 'Periodes de reporting',
        'niveau_priorite' => 'Niveaux de priorite',
        'type_risque' => 'Types de risques',
        'categorie_alerte' => 'Categories d alertes',
        'niveau_criticite' => 'Niveaux de criticite',
        'type_objectif' => 'Types d objectifs',
        'type_resultat' => 'Types de resultats',
        'source_donnees' => 'Sources de donnees',
        'type_notification' => 'Types de notifications',
        'type_dependance' => 'Types de dependances',
        'type_commentaire' => 'Types de commentaires',
    ];

    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.referentiels.voir');

        $stats = collect(self::TYPES)->map(fn ($libelle, $type) => [
            'type' => $type,
            'libelle' => $libelle,
            'total' => Referentiel::query()->where('type', $type)->count(),
            'actifs' => Referentiel::query()->where('type', $type)->where('actif', true)->count(),
        ]);

        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.referentiels.index', compact('stats', 'scopeLabel'));
    }

    public function liste(Request $request, string $type)
    {
        $this->authorize('parametres.referentiels.voir');
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $referentiels = Referentiel::query()
            ->where('type', $type)
            ->orderBy('ordre')
            ->orderBy('libelle')
            ->get();

        $libelleType = self::TYPES[$type];
        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.referentiels.liste', compact('referentiels', 'type', 'libelleType', 'scopeLabel'));
    }

    public function store(Request $request, string $type)
    {
        $this->authorize('parametres.referentiels.gerer');
        $this->ensureGlobalScope($request);
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $data = $request->validate([
            'code' => 'required|string|max:40',
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:60',
            'description' => 'nullable|string|max:500',
            'couleur' => 'nullable|string|max:20',
            'ordre' => 'nullable|integer|min:0',
        ]);

        $exists = Referentiel::query()
            ->where('type', $type)
            ->where('code', strtoupper($data['code']))
            ->exists();

        abort_if($exists, 422, "Le code {$data['code']} existe deja pour ce referentiel.");

        $data['code'] = strtoupper($data['code']);

        $referentiel = Referentiel::create(array_merge($data, [
            'type' => $type,
            'actif' => true,
            'cree_par' => $request->user()->id,
        ]));

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'referentiel_cree',
            auditable: $referentiel,
            acteur: $request->user(),
            action: 'creer',
            description: "Referentiel {$type}/{$referentiel->code} cree",
        );

        return back()->with('success', "Entree \"{$referentiel->libelle}\" ajoutee.");
    }

    public function update(Request $request, string $type, Referentiel $referentiel)
    {
        $this->authorize('parametres.referentiels.gerer');
        $this->ensureGlobalScope($request);
        abort_if($referentiel->est_systeme, 403, 'Ce referentiel systeme ne peut pas etre modifie.');

        $data = $request->validate([
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:60',
            'description' => 'nullable|string|max:500',
            'couleur' => 'nullable|string|max:20',
            'ordre' => 'nullable|integer|min:0',
        ]);

        $avant = $referentiel->toArray();
        $referentiel->update(array_merge($data, ['modifie_par' => $request->user()->id]));

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'referentiel_modifie',
            auditable: $referentiel,
            acteur: $request->user(),
            action: 'modifier',
            description: "Referentiel {$type}/{$referentiel->code} modifie",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Referentiel \"{$referentiel->libelle}\" mis a jour.");
    }

    public function toggle(Request $request, string $type, Referentiel $referentiel)
    {
        $this->authorize('parametres.referentiels.gerer');
        $this->ensureGlobalScope($request);
        abort_if($referentiel->est_systeme && $referentiel->actif, 403, 'Ce referentiel systeme actif ne peut pas etre desactive.');

        $referentiel->update(['actif' => ! $referentiel->actif]);
        $etat = $referentiel->actif ? 'active' : 'desactivee';

        return back()->with('success', "\"{$referentiel->libelle}\" {$etat}.");
    }

    public function destroy(Request $request, string $type, Referentiel $referentiel)
    {
        $this->authorize('parametres.referentiels.gerer');
        $this->ensureGlobalScope($request);
        abort_if($referentiel->est_systeme, 403, 'Ce referentiel systeme ne peut pas etre supprime. Desactivez-le a la place.');

        $referentiel->delete();

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'referentiel_supprime',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'supprimer',
            description: "Referentiel {$type}/{$referentiel->code} supprime",
        );

        return back()->with('success', "Referentiel \"{$referentiel->libelle}\" supprime.");
    }

    public function reordonner(Request $request, string $type)
    {
        $this->authorize('parametres.referentiels.gerer');
        $this->ensureGlobalScope($request);
        $request->validate(['ordre' => 'required|array', 'ordre.*' => 'integer']);

        foreach ($request->ordre as $position => $id) {
            Referentiel::query()->where('id', $id)->where('type', $type)->update(['ordre' => $position]);
        }

        return response()->json(['ok' => true]);
    }

    private function ensureGlobalScope(Request $request): void
    {
        abort_unless($request->user()->resolveVisibilityScope()->isGlobal, 403);
    }

    private function scopeLabel(Request $request): string
    {
        return $request->user()->resolveVisibilityScope()->isGlobal
            ? 'Perimetre de donnees : Consolidation institutionnelle'
            : $request->user()->scopeLabel();
    }
}
