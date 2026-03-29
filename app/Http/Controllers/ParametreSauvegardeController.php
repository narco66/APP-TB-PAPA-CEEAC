<?php

namespace App\Http\Controllers;

use App\Models\LibelleMetier;
use App\Models\Parametre;
use App\Models\Referentiel;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParametreSauvegardeController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $this->authorize('parametres.sauvegardes.voir');

        $stats = [
            'parametres'   => Parametre::count(),
            'referentiels' => Referentiel::count(),
            'libelles'     => LibelleMetier::count(),
        ];

        return view('parametres.sauvegardes.index', compact('stats'));
    }

    public function exporter(Request $request, string $type)
    {
        $this->authorize('parametres.sauvegardes.exporter');

        abort_unless(in_array($type, ['parametres', 'referentiels', 'libelles', 'tout']), 404);

        $data = [];
        $filename = "export_{$type}_" . now()->format('Ymd_His') . '.json';

        if (in_array($type, ['parametres', 'tout'])) {
            $data['parametres'] = Parametre::all()->map(fn($p) => [
                'cle'           => $p->cle,
                'groupe'        => $p->groupe,
                'type'          => $p->type,
                'valeur'        => $p->est_sensible ? '***' : $p->valeur,
                'valeur_defaut' => $p->valeur_defaut,
                'libelle'       => $p->libelle,
                'est_systeme'   => $p->est_systeme,
            ])->toArray();
        }

        if (in_array($type, ['referentiels', 'tout'])) {
            $data['referentiels'] = Referentiel::all()->map(fn($r) => [
                'type'          => $r->type,
                'code'          => $r->code,
                'libelle'       => $r->libelle,
                'libelle_court' => $r->libelle_court,
                'description'   => $r->description,
                'ordre'         => $r->ordre,
                'actif'         => $r->actif,
                'est_systeme'   => $r->est_systeme,
            ])->toArray();
        }

        if (in_array($type, ['libelles', 'tout'])) {
            $data['libelles_metier'] = LibelleMetier::all()->map(fn($l) => [
                'cle'             => $l->cle,
                'module'          => $l->module,
                'valeur_defaut'   => $l->valeur_defaut,
                'valeur_courante' => $l->valeur_courante,
                'valeur_courte'   => $l->valeur_courte,
                'est_systeme'     => $l->est_systeme,
            ])->toArray();
        }

        $data['meta'] = [
            'exporte_le'  => now()->toIso8601String(),
            'exporte_par' => $request->user()->name,
            'version'     => '1.0',
            'type'        => $type,
        ];

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'sauvegarde_exportee',
            auditable: null,
            acteur: $request->user(),
            action: 'exporter',
            description: "Export JSON des paramètres — type : {$type}",
        );

        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Type'        => 'application/json',
        ]);
    }

    public function importer(Request $request)
    {
        $this->authorize('parametres.sauvegardes.importer');

        $request->validate([
            'fichier'      => 'required|file|mimes:json|max:2048',
            'confirmation' => 'required|in:IMPORTER',
        ], [
            'confirmation.in' => 'Tapez exactement "IMPORTER" pour confirmer.',
        ]);

        $contenu = json_decode(file_get_contents($request->file('fichier')->getRealPath()), true);
        abort_if(!$contenu || !isset($contenu['meta']), 422, 'Fichier JSON invalide ou non reconnu.');

        $nb = 0;

        if (isset($contenu['parametres'])) {
            foreach ($contenu['parametres'] as $p) {
                if (!isset($p['cle'])) continue;
                $existing = Parametre::where('cle', $p['cle'])->first();
                if ($existing && $existing->est_systeme) continue; // Never overwrite system params
                Parametre::updateOrCreate(['cle' => $p['cle']], [
                    'groupe'        => $p['groupe'] ?? 'general',
                    'type'          => $p['type'] ?? 'string',
                    'valeur'        => $p['valeur'] !== '***' ? $p['valeur'] : $existing?->valeur,
                    'valeur_defaut' => $p['valeur_defaut'] ?? null,
                    'libelle'       => $p['libelle'] ?? $p['cle'],
                ]);
                $nb++;
            }
        }

        if (isset($contenu['referentiels'])) {
            foreach ($contenu['referentiels'] as $r) {
                if (!isset($r['type'], $r['code'])) continue;
                Referentiel::updateOrCreate(['type' => $r['type'], 'code' => $r['code']], [
                    'libelle'       => $r['libelle'] ?? $r['code'],
                    'libelle_court' => $r['libelle_court'] ?? null,
                    'description'   => $r['description'] ?? null,
                    'ordre'         => $r['ordre'] ?? 99,
                    'actif'         => $r['actif'] ?? true,
                ]);
                $nb++;
            }
        }

        if (isset($contenu['libelles_metier'])) {
            foreach ($contenu['libelles_metier'] as $l) {
                if (!isset($l['cle'])) continue;
                $existing = LibelleMetier::where('cle', $l['cle'])->first();
                if ($existing && $existing->est_systeme) continue;
                LibelleMetier::updateOrCreate(['cle' => $l['cle']], [
                    'module'          => $l['module'] ?? 'general',
                    'valeur_defaut'   => $l['valeur_defaut'] ?? $l['cle'],
                    'valeur_courante' => $l['valeur_courante'] ?? null,
                    'valeur_courte'   => $l['valeur_courte'] ?? null,
                ]);
                $nb++;
            }
        }

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'sauvegarde_importee',
            auditable: null,
            acteur: $request->user(),
            action: 'importer',
            description: "Import JSON — {$nb} entrées importées depuis {$contenu['meta']['exporte_le']}",
        );

        return back()->with('success', "{$nb} entrées importées avec succès.");
    }
}
