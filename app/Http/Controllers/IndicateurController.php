<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndicateurRequest;
use App\Models\Indicateur;
use App\Models\ValeurIndicateur;
use App\Models\Direction;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;

class IndicateurController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('indicateur.voir');
        $user = $request->user();

        $query = Indicateur::with(['direction', 'responsable', 'resultatAttendu'])
            ->actif()
            ->visibleTo($user)
            ->orderBy('code');

        if ($request->filled('type')) {
            $query->where('type_indicateur', $request->type);
        }
        if ($request->filled('niveau_alerte')) {
            // Filtrage post-requête (calculé)
        }

        $indicateurs = $query->paginate(20)->withQueryString();
        $scopeLabel = $user->scopeLabel();

        return view('indicateurs.index', compact('indicateurs', 'scopeLabel'));
    }

    public function create()
    {
        $this->authorize('indicateur.creer');
        $user = request()->user();
        $resolver = app(UserScopeResolver::class);
        $directions = $resolver
            ->applyToQuery(Direction::actif()->orderBy('libelle'), $user, [
                'departement' => 'departement_id',
                'direction' => 'id',
                'service' => null,
            ])
            ->get();
        $users = $resolver
            ->applyToQuery(User::actif()->orderBy('name'), $user, [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get();
        $scopeLabel = $user->scopeLabel();

        return view('indicateurs.create', compact('directions', 'users', 'scopeLabel'));
    }

    public function store(StoreIndicateurRequest $request)
    {
        $indicateur = Indicateur::create($request->validated());

        return redirect()
            ->route('indicateurs.show', $indicateur)
            ->with('success', "Indicateur {$indicateur->code} créé.");
    }

    public function show(Indicateur $indicateur)
    {
        $this->authorize('voir', $indicateur);
        $user = auth()->user();

        $indicateur->load(['direction', 'responsable', 'valeurs' => fn($q) => $q->orderBy('annee')->orderBy('mois')->orderBy('trimestre')]);

        $valeursCourbes = $indicateur->valeurs->map(fn($v) => [
            'periode' => $v->periode_libelle,
            'realise' => $v->valeur_realisee,
            'cible'   => $v->valeur_cible_periode,
            'taux'    => $v->taux_realisation,
        ])->values()->toArray();

        return view('indicateurs.show', [
            'indicateur' => $indicateur,
            'valeursCourbes' => $valeursCourbes,
            'scopeLabel' => $user->scopeLabel(),
        ]);
    }

    public function print(Indicateur $indicateur)
    {
        $this->authorize('voir', $indicateur);
        $user = auth()->user();

        $indicateur->load([
            'direction',
            'responsable',
            'valeurs' => fn ($q) => $q
                ->with(['saisiPar', 'validePar'])
                ->orderByDesc('annee')
                ->orderByDesc('trimestre')
                ->orderByDesc('mois'),
        ]);

        return view('indicateurs.print', [
            'indicateur' => $indicateur,
            'scopeLabel' => $user->scopeLabel(),
            'printedAt' => now(),
        ]);
    }

    public function edit(Indicateur $indicateur)
    {
        $this->authorize('modifier', $indicateur);
        $user = request()->user();
        $resolver = app(UserScopeResolver::class);
        $directions = $resolver
            ->applyToQuery(Direction::actif()->orderBy('libelle'), $user, [
                'departement' => 'departement_id',
                'direction' => 'id',
                'service' => null,
            ])
            ->get();
        $users = $resolver
            ->applyToQuery(User::actif()->orderBy('name'), $user, [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get();
        $scopeLabel = $user->scopeLabel();

        return view('indicateurs.edit', compact('indicateur', 'directions', 'users', 'scopeLabel'));
    }

    public function update(Request $request, Indicateur $indicateur)
    {
        $this->authorize('modifier', $indicateur);

        $data = $request->validate([
            'libelle'               => 'required|string|max:500',
            'definition'            => 'nullable|string',
            'unite_mesure'          => 'nullable|string|max:50',
            'valeur_cible_annuelle' => 'nullable|numeric',
            'responsable_id'        => 'nullable|exists:users,id',
            'direction_id'          => 'nullable|exists:directions,id',
            'seuil_alerte_rouge'    => 'nullable|numeric|min:0|max:100',
            'seuil_alerte_orange'   => 'nullable|numeric|min:0|max:100',
            'seuil_alerte_vert'     => 'nullable|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
        ]);

        $resolver = app(UserScopeResolver::class);
        $currentUser = $request->user();

        if (! empty($data['direction_id'])) {
            $direction = Direction::find($data['direction_id']);

            abort_unless(
                $direction && $resolver->canAccessAttributes($currentUser, departementId: $direction->departement_id, directionId: $direction->id),
                403
            );
        }

        if (! empty($data['responsable_id'])) {
            $responsable = User::find($data['responsable_id']);

            abort_unless(
                $responsable && $resolver->canAccessAttributes(
                    $currentUser,
                    departementId: $responsable->departement_id ?? $responsable->direction?->departement_id,
                    directionId: $responsable->direction_id,
                    serviceId: $responsable->service_id,
                ),
                403
            );
        }

        $indicateur->update($data);

        return redirect()
            ->route('indicateurs.show', $indicateur)
            ->with('success', 'Indicateur mis à jour.');
    }

    public function saisirValeur(Request $request, Indicateur $indicateur)
    {
        $this->authorize('saisirValeur', $indicateur);

        $data = $request->validate([
            'periode_type'        => 'required|in:mensuelle,trimestrielle,semestrielle,annuelle',
            'periode_libelle'     => 'required|string|max:30',
            'annee'               => 'required|integer|min:2020|max:2050',
            'mois'                => 'nullable|integer|min:1|max:12',
            'trimestre'           => 'nullable|integer|min:1|max:4',
            'semestre'            => 'nullable|integer|min:1|max:2',
            'valeur_realisee'     => 'nullable|numeric',
            'valeur_cible_periode' => 'nullable|numeric',
            'commentaire'         => 'nullable|string|max:2000',
            'analyse_ecart'       => 'nullable|string|max:2000',
        ]);

        // Calculer le taux de réalisation
        if (!empty($data['valeur_cible_periode']) && $data['valeur_cible_periode'] > 0 && !empty($data['valeur_realisee'])) {
            $data['taux_realisation'] = min(100, round(($data['valeur_realisee'] / $data['valeur_cible_periode']) * 100, 2));
        } else {
            $data['taux_realisation'] = 0;
        }

        $data['statut_validation'] = 'brouillon';
        $data['saisi_par']         = $request->user()->id;

        $valeur = ValeurIndicateur::updateOrCreate(
            [
                'indicateur_id' => $indicateur->id,
                'periode_type'  => $data['periode_type'],
                'annee'         => $data['annee'],
                'mois'          => $data['mois'] ?? null,
                'trimestre'     => $data['trimestre'] ?? null,
                'semestre'      => $data['semestre'] ?? null,
            ],
            $data
        );

        // Mettre à jour le taux courant de l'indicateur
        $indicateur->update([
            'taux_realisation_courant' => $data['taux_realisation'],
        ]);

        return redirect()
            ->route('indicateurs.show', $indicateur)
            ->with('success', 'Valeur saisie avec succès.');
    }

    public function validerValeur(Request $request, ValeurIndicateur $valeur)
    {
        $this->authorize('validerValeur', $valeur->indicateur);

        $request->validate(['action' => 'required|in:valide,rejete', 'motif' => 'nullable|string|max:500']);

        if ($request->action === 'valide') {
            $valeur->update([
                'statut_validation' => 'valide',
                'valide_par'        => $request->user()->id,
                'valide_le'         => now(),
            ]);
        } else {
            $valeur->update([
                'statut_validation' => 'rejete',
                'motif_rejet'       => $request->motif,
            ]);
        }

        return back()->with('success', 'Valeur ' . ($request->action === 'valide' ? 'validée' : 'rejetée') . '.');
    }
}
