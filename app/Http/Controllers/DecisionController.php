<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\Decision;
use App\Models\Document;
use App\Models\Papa;
use App\Services\DecisionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DecisionController extends Controller
{
    public function __construct(private DecisionService $decisionService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Decision::class);

        $query = Decision::with(['papa', 'actionPrioritaire', 'activite', 'prisePar', 'valideePar'])
            ->orderByDesc('created_at');

        if ($request->filled('papa_id')) {
            $query->where('papa_id', $request->integer('papa_id'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        if ($request->filled('niveau_decision')) {
            $query->where('niveau_decision', $request->string('niveau_decision'));
        }

        $decisions = $query->paginate(20);
        $papas = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);

        return view('decisions.index', compact('decisions', 'papas'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Decision::class);

        $papa = null;
        if ($request->filled('papa_id')) {
            $papa = Papa::find($request->integer('papa_id'));
        }

        $papas = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $actions = collect();

        if ($papa) {
            $actions = ActionPrioritaire::where('papa_id', $papa->id)
                ->orderBy('ordre')
                ->get(['id', 'code', 'libelle']);
        }

        return view('decisions.create', compact('papas', 'papa', 'actions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Decision::class);

        $data = $request->validate([
            'reference' => 'nullable|string|max:100',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type_decision' => 'required|in:arbitrage,validation,orientation,reaffectation_budgetaire,report,suspension',
            'niveau_decision' => 'required|in:direction,commissaire,sg,presidence',
            'statut' => 'nullable|in:brouillon,soumise,validee,rejetee,executee,archivee',
            'papa_id' => 'nullable|exists:papas,id',
            'action_prioritaire_id' => 'nullable|exists:action_prioritaires,id',
            'activite_id' => 'nullable|exists:activites,id',
            'budget_papa_id' => 'nullable|exists:budgets_papa,id',
            'impact_budgetaire' => 'nullable|numeric|min:0',
            'impact_calendrier_jours' => 'nullable|integer',
            'mise_en_oeuvre_obligatoire' => 'nullable|boolean',
            'date_effet' => 'nullable|date',
        ]);

        $data['reference'] = $data['reference'] ?? $this->genererReference();
        $data['statut'] = $data['statut'] ?? 'brouillon';
        $data['mise_en_oeuvre_obligatoire'] = (bool) ($data['mise_en_oeuvre_obligatoire'] ?? false);

        $decision = $this->decisionService->creer($data, $request->user());

        return redirect()
            ->route('decisions.show', $decision)
            ->with('success', "Décision {$decision->reference} créée.");
    }

    public function show(Decision $decision)
    {
        $this->authorize('view', $decision);

        $decision->load(['papa', 'actionPrioritaire', 'activite', 'prisePar', 'valideePar', 'attachments.document.categorie', 'attachments.validePar']);

        $documents = Document::query()
            ->with('categorie')
            ->latest('created_at')
            ->limit(50)
            ->get(['id', 'titre', 'statut', 'categorie_id']);

        return view('decisions.show', compact('decision', 'documents'));
    }

    public function audit(Decision $decision): RedirectResponse
    {
        $this->authorize('view', $decision);

        return redirect()->route('admin.audit-events', $decision->auditTrailParams());
    }

    public function valider(Request $request, Decision $decision)
    {
        $this->authorize('valider', $decision);

        $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $this->decisionService->valider($decision, $request->user(), $request->string('commentaire')->toString() ?: null);

        return redirect()
            ->route('decisions.show', $decision)
            ->with('success', "Décision {$decision->reference} validée.");
    }

    public function executer(Request $request, Decision $decision)
    {
        $this->authorize('executer', $decision);

        $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $this->decisionService->executer($decision, $request->user(), $request->string('commentaire')->toString() ?: null);

        return redirect()
            ->route('decisions.show', $decision)
            ->with('success', "Décision {$decision->reference} exécutée.");
    }

    public function rattacherDocument(Request $request, Decision $decision)
    {
        $this->authorize('rattacherDocument', $decision);

        $data = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'type_piece' => 'required|string|max:100',
            'obligatoire' => 'nullable|boolean',
        ]);

        $document = Document::findOrFail($data['document_id']);

        $this->decisionService->rattacherDocument(
            $decision,
            $document,
            $request->user(),
            $data['type_piece'],
            (bool) ($data['obligatoire'] ?? true),
        );

        return redirect()
            ->route('decisions.show', $decision)
            ->with('success', 'Document rattaché à la décision.');
    }

    private function genererReference(): string
    {
        do {
            $reference = 'DEC-' . now()->format('Y') . '-' . strtoupper(Str::random(6));
        } while (Decision::where('reference', $reference)->exists());

        return $reference;
    }
}
