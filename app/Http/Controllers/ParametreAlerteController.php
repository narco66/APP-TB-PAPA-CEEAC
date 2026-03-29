<?php

namespace App\Http\Controllers;

use App\Models\NotificationRule;
use App\Models\Parametre;
use App\Services\AuditService;
use App\Services\ParametreService;
use Illuminate\Http\Request;

class ParametreAlerteController extends Controller
{
    public function __construct(
        private ParametreService $parametreService,
        private AuditService $auditService,
    ) {}

    public function index()
    {
        $this->authorize('parametres.alertes.voir');

        $rules = NotificationRule::orderBy('event_type')->orderBy('canal')->get();
        $seuils = $this->parametreService->getGroupe('alertes');

        return view('parametres.alertes.index', compact('rules', 'seuils'));
    }

    public function saveSeuils(Request $request)
    {
        $this->authorize('parametres.alertes.modifier');

        $data = $request->validate([
            'alerte_seuil_retard_jours' => 'required|integer|min:1|max:90',
            'alerte_seuil_budget_pct'   => 'required|integer|min:1|max:100',
        ]);

        $avant = $this->parametreService->getGroupe('alertes');
        $this->parametreService->saveGroupe('alertes', $data, $request->user());

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'seuils_alerte_modifies',
            auditable: null,
            acteur: $request->user(),
            action: 'modifier',
            description: 'Seuils d\'alerte modifiés',
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', 'Seuils d\'alerte sauvegardés.');
    }

    public function updateRule(Request $request, NotificationRule $rule)
    {
        $this->authorize('parametres.alertes.modifier');

        $data = $request->validate([
            'libelle'          => 'required|string|max:200',
            'delai_minutes'    => 'nullable|integer|min:0',
            'template_sujet'   => 'nullable|string|max:300',
            'template_message' => 'nullable|string|max:2000',
            'actif'            => 'boolean',
        ]);

        $avant = $rule->toArray();
        $rule->update($data);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'notification_rule_modifiee',
            auditable: $rule,
            acteur: $request->user(),
            action: 'modifier',
            description: "Règle de notification {$rule->code} modifiée",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Règle \"{$rule->libelle}\" mise à jour.");
    }

    public function toggleRule(Request $request, NotificationRule $rule)
    {
        $this->authorize('parametres.alertes.modifier');
        $rule->update(['actif' => !$rule->actif]);
        $etat = $rule->actif ? 'activée' : 'désactivée';
        return back()->with('success', "Règle \"{$rule->libelle}\" {$etat}.");
    }
}
