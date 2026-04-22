<?php

namespace App\Http\Controllers;

use App\Models\LibelleMetier;
use App\Models\Parametre;
use App\Models\Referentiel;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ParametreSauvegardeController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.sauvegardes.voir');

        $stats = [
            'parametres' => Parametre::count(),
            'referentiels' => Referentiel::count(),
            'libelles' => LibelleMetier::count(),
        ];

        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.sauvegardes.index', compact('stats', 'scopeLabel'));
    }

    public function exporter(Request $request, string $type)
    {
        $this->authorize('parametres.sauvegardes.exporter');
        $this->ensureGlobalScope($request);

        abort_unless(in_array($type, ['parametres', 'referentiels', 'libelles', 'tout'], true), 404);

        $data = [];
        $filename = "export_{$type}_" . now()->format('Ymd_His') . '.json';

        if (in_array($type, ['parametres', 'tout'], true)) {
            $data['parametres'] = Parametre::all()->map(fn ($p) => [
                'cle' => $p->cle,
                'groupe' => $p->groupe,
                'type' => $p->type,
                'valeur' => $p->est_sensible ? '***' : $p->valeur,
                'valeur_defaut' => $p->valeur_defaut,
                'libelle' => $p->libelle,
                'est_systeme' => $p->est_systeme,
            ])->toArray();
        }

        if (in_array($type, ['referentiels', 'tout'], true)) {
            $data['referentiels'] = Referentiel::all()->map(fn ($r) => [
                'type' => $r->type,
                'code' => $r->code,
                'libelle' => $r->libelle,
                'libelle_court' => $r->libelle_court,
                'description' => $r->description,
                'ordre' => $r->ordre,
                'actif' => $r->actif,
                'est_systeme' => $r->est_systeme,
            ])->toArray();
        }

        if (in_array($type, ['libelles', 'tout'], true)) {
            $data['libelles_metier'] = LibelleMetier::all()->map(fn ($l) => [
                'cle' => $l->cle,
                'module' => $l->module,
                'valeur_defaut' => $l->valeur_defaut,
                'valeur_courante' => $l->valeur_courante,
                'valeur_courte' => $l->valeur_courte,
                'est_systeme' => $l->est_systeme,
            ])->toArray();
        }

        $data['meta'] = [
            'exporte_le' => now()->toIso8601String(),
            'exporte_par' => $request->user()->name,
            'version' => '1.0',
            'type' => $type,
            'scope_label' => 'Perimetre de donnees : Consolidation institutionnelle',
        ];

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'sauvegarde_exportee',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'exporter',
            description: "Export JSON des parametres - type : {$type}",
        );

        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Type' => 'application/json',
        ]);
    }

    public function importer(Request $request)
    {
        $this->authorize('parametres.sauvegardes.importer');
        $this->ensureGlobalScope($request);

        $request->validate([
            'fichier' => 'required|file|mimes:json|max:2048',
            'confirmation' => 'required|in:IMPORTER',
        ], [
            'confirmation.in' => 'Tapez exactement "IMPORTER" pour confirmer.',
        ]);

        $contenu = json_decode(file_get_contents($request->file('fichier')->getRealPath()), true);
        abort_if(! $contenu || ! isset($contenu['meta']), 422, 'Fichier JSON invalide ou non reconnu.');

        $nb = 0;

        if (isset($contenu['parametres'])) {
            foreach ($contenu['parametres'] as $p) {
                if (! isset($p['cle'])) {
                    continue;
                }

                $existing = Parametre::where('cle', $p['cle'])->first();
                if ($existing && $existing->est_systeme) {
                    continue;
                }

                Parametre::updateOrCreate(['cle' => $p['cle']], [
                    'groupe' => $p['groupe'] ?? 'general',
                    'type' => $p['type'] ?? 'string',
                    'valeur' => ($p['valeur'] ?? null) !== '***' ? ($p['valeur'] ?? null) : $existing?->valeur,
                    'valeur_defaut' => $p['valeur_defaut'] ?? null,
                    'libelle' => $p['libelle'] ?? $p['cle'],
                ]);
                $nb++;
            }
        }

        if (isset($contenu['referentiels'])) {
            foreach ($contenu['referentiels'] as $r) {
                if (! isset($r['type'], $r['code'])) {
                    continue;
                }

                Referentiel::updateOrCreate(['type' => $r['type'], 'code' => $r['code']], [
                    'libelle' => $r['libelle'] ?? $r['code'],
                    'libelle_court' => $r['libelle_court'] ?? null,
                    'description' => $r['description'] ?? null,
                    'ordre' => $r['ordre'] ?? 99,
                    'actif' => $r['actif'] ?? true,
                ]);
                $nb++;
            }
        }

        if (isset($contenu['libelles_metier'])) {
            foreach ($contenu['libelles_metier'] as $l) {
                if (! isset($l['cle'])) {
                    continue;
                }

                $existing = LibelleMetier::where('cle', $l['cle'])->first();
                if ($existing && $existing->est_systeme) {
                    continue;
                }

                LibelleMetier::updateOrCreate(['cle' => $l['cle']], [
                    'module' => $l['module'] ?? 'general',
                    'valeur_defaut' => $l['valeur_defaut'] ?? $l['cle'],
                    'valeur_courante' => $l['valeur_courante'] ?? null,
                    'valeur_courte' => $l['valeur_courte'] ?? null,
                ]);
                $nb++;
            }
        }

        $exporteLe = $contenu['meta']['exporte_le'] ?? 'date inconnue';

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'sauvegarde_importee',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'importer',
            description: "Import JSON - {$nb} entrees importees depuis {$exporteLe}",
        );

        return back()->with('success', "{$nb} entrees importees avec succes.");
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
