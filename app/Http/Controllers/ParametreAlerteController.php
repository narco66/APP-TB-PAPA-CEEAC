<?php

namespace App\Http\Controllers;

use App\Models\NotificationRule;
use App\Services\AuditService;
use App\Services\ParametreService;
use Illuminate\Http\Request;

class ParametreAlerteController extends Controller
{
    public function __construct(
        private ParametreService $parametreService,
        private AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.alertes.voir');

        $rules = NotificationRule::orderBy('event_type')->orderBy('canal')->get();
        $seuils = $this->parametreService->getGroupe('alertes');
        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.alertes.index', compact('rules', 'seuils', 'scopeLabel'));
    }

    public function saveSeuils(Request $request)
    {
        $this->authorize('parametres.alertes.modifier');
        $this->ensureGlobalScope($request);

        $data = $request->validate([
            'alerte_seuil_retard_jours' => 'required|integer|min:1|max:90',
            'alerte_seuil_budget_pct' => 'required|integer|min:1|max:100',
        ]);

        $avant = $this->parametreService->getGroupe('alertes');
        $this->parametreService->saveGroupe('alertes', $data, $request->user());

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'seuils_alerte_modifies',
            auditable: null,
            acteur: $request->user(),
            action: 'modifier',
            description: 'Seuils d alerte modifies',
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', 'Seuils d alerte sauvegardes.');
    }

    public function updateRule(Request $request, NotificationRule $rule)
    {
        $this->authorize('parametres.alertes.modifier');
        $this->ensureGlobalScope($request);

        $data = $request->validate([
            'libelle' => 'required|string|max:200',
            'delai_minutes' => 'nullable|integer|min:0',
            'template_sujet' => 'nullable|string|max:300',
            'template_message' => 'nullable|string|max:2000',
            'actif' => 'boolean',
        ]);

        $avant = $rule->toArray();
        $rule->update($data);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'notification_rule_modifiee',
            auditable: $rule,
            acteur: $request->user(),
            action: 'modifier',
            description: "Regle de notification {$rule->code} modifiee",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Regle \"{$rule->libelle}\" mise a jour.");
    }

    public function toggleRule(Request $request, NotificationRule $rule)
    {
        $this->authorize('parametres.alertes.modifier');
        $this->ensureGlobalScope($request);

        $rule->update(['actif' => ! $rule->actif]);
        $etat = $rule->actif ? 'activee' : 'desactivee';

        return back()->with('success', "Regle \"{$rule->libelle}\" {$etat}.");
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
