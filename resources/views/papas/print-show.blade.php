<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $papa->code }} - Version imprimable</title>
    <style>
        :root { --text:#1f2937; --muted:#6b7280; --line:#d1d5db; --soft:#f3f4f6; --brand:#1d4ed8; --ok:#166534; --warn:#b45309; --danger:#b91c1c; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Arial,sans-serif; color:var(--text); background:#fff; }
        .page { max-width:1180px; margin:0 auto; padding:24px; }
        .toolbar { display:flex; justify-content:flex-end; gap:12px; margin-bottom:20px; }
        .button { display:inline-block; padding:10px 14px; border:1px solid var(--line); border-radius:8px; color:var(--text); text-decoration:none; background:#fff; font-size:14px; }
        .button.primary { background:var(--brand); border-color:var(--brand); color:#fff; }
        .header { border-bottom:2px solid var(--brand); padding-bottom:16px; margin-bottom:20px; }
        .title { font-size:28px; font-weight:700; margin:6px 0; }
        .subtitle { font-size:15px; color:var(--muted); }
        .meta { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:12px; margin-top:18px; }
        .card { border:1px solid var(--line); border-radius:10px; padding:12px; background:#fff; }
        .label { font-size:12px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; letter-spacing:.04em; }
        .value { font-size:14px; font-weight:600; }
        .section { margin-top:24px; }
        .section-title { font-size:18px; font-weight:700; margin:0 0 12px; }
        .stats { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:12px; }
        table { width:100%; border-collapse:collapse; font-size:13px; }
        th, td { border:1px solid var(--line); padding:8px 10px; text-align:left; vertical-align:top; }
        th { background:var(--soft); font-size:12px; text-transform:uppercase; color:var(--muted); }
        .muted { color:var(--muted); }
        .empty { border:1px dashed var(--line); border-radius:10px; padding:16px; color:var(--muted); background:#fafafa; }
        .footer { margin-top:24px; padding-top:12px; border-top:1px solid var(--line); font-size:12px; color:var(--muted); }
        @media print {
            .toolbar { display:none; }
            .page { max-width:none; padding:0; }
            a { color:inherit; text-decoration:none; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="toolbar">
        <a href="{{ route('papas.show', $papa) }}" class="button">Retour à la fiche</a>
        <button type="button" onclick="window.print()" class="button primary">Imprimer</button>
    </div>

    <div class="header">
        <div class="label">Plan d'Action Prioritaire Annuel</div>
        <div class="title">{{ $papa->code }} - {{ $papa->libelle }}</div>
        <div class="subtitle">{{ $scopeLabel }}</div>
        <div class="meta">
            <div class="card">
                <div class="label">Période</div>
                <div class="value">{{ $papa->date_debut->format('d/m/Y') }} - {{ $papa->date_fin->format('d/m/Y') }}</div>
            </div>
            <div class="card">
                <div class="label">Statut</div>
                <div class="value">{{ $papa->libelleStatut() }}</div>
            </div>
            <div class="card">
                <div class="label">Budget total prévu</div>
                <div class="value">{{ number_format($papa->budget_total_prevu, 0, ',', ' ') }} {{ $papa->devise }}</div>
            </div>
            <div class="card">
                <div class="label">Imprimé le</div>
                <div class="value">{{ $printedAt->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="stats">
            <div class="card">
                <div class="label">Exécution physique</div>
                <div class="value">{{ $papa->taux_execution_physique }}%</div>
            </div>
            <div class="card">
                <div class="label">Exécution financière</div>
                <div class="value">{{ $papa->taux_execution_financiere }}%</div>
            </div>
            <div class="card">
                <div class="label">Actions prioritaires</div>
                <div class="value">{{ $papa->actionsPrioritaires->count() }}</div>
            </div>
            <div class="card">
                <div class="label">Risques</div>
                <div class="value">{{ $papa->risques->count() }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Synthèse</h2>
        <table>
            <tbody>
                <tr>
                    <th style="width:25%">Créé par</th>
                    <td>{{ $papa->creePar?->nomComplet() ?? 'Non renseigné' }}</td>
                    <th style="width:25%">Validé par</th>
                    <td>{{ $papa->validePar?->nomComplet() ?? 'Non renseigné' }}</td>
                </tr>
                <tr>
                    <th>Année</th>
                    <td>{{ $papa->annee }}</td>
                    <th>Verrouillage</th>
                    <td>{{ $papa->est_verrouille ? 'Oui' : 'Non' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Actions prioritaires</h2>
        @if($papa->actionsPrioritaires->isEmpty())
            <div class="empty">Aucune action prioritaire visible.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Libellé</th>
                        <th>Département</th>
                        <th>Objectifs</th>
                        <th>Résultats</th>
                        <th>Statut</th>
                        <th>Taux</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($papa->actionsPrioritaires as $action)
                        <tr>
                            <td>{{ $action->code }}</td>
                            <td>{{ $action->libelle }}</td>
                            <td>{{ $action->departement?->libelleAffichage() ?? 'Non renseigné' }}</td>
                            <td>{{ $action->objectifsImmediat->count() }}</td>
                            <td>{{ $action->objectifsImmediat->sum('resultats_attendus_count') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $action->statut)) }}</td>
                            <td>{{ number_format($action->taux_realisation, 0) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Budget</h2>
        @if($papa->budgets->isEmpty())
            <div class="empty">Aucune ligne budgétaire visible.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Action prioritaire</th>
                        <th>Partenaire</th>
                        <th>Année</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($papa->budgets as $budget)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $budget->source_financement)) }}</td>
                            <td>{{ $budget->actionPrioritaire?->code ?? 'Non renseignée' }}</td>
                            <td>{{ $budget->partenaire?->nom ?? 'Non renseigné' }}</td>
                            <td>{{ $budget->annee_budgetaire }}</td>
                            <td>{{ number_format($budget->montant, 0, ',', ' ') }} {{ $budget->devise ?? $papa->devise }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Risques</h2>
        @if($papa->risques->isEmpty())
            <div class="empty">Aucun risque visible.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Intitulé</th>
                        <th>Catégorie</th>
                        <th>Probabilité</th>
                        <th>Impact</th>
                        <th>Niveau</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($papa->risques as $risque)
                        <tr>
                            <td>{{ $risque->intitule ?? $risque->libelle ?? 'Risque' }}</td>
                            <td>{{ $risque->categorie ?? 'Non renseignée' }}</td>
                            <td>{{ $risque->probabilite ?? 'N/A' }}</td>
                            <td>{{ $risque->impact ?? 'N/A' }}</td>
                            <td>{{ $risque->niveau_risque ?? $risque->niveau ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Workflow et décisions</h2>
        <div class="stats">
            <div class="card">
                <div class="label">Workflows</div>
                <div class="value">{{ $papa->workflowInstances->count() }}</div>
            </div>
            <div class="card">
                <div class="label">Décisions</div>
                <div class="value">{{ $papa->decisions->count() }}</div>
            </div>
            <div class="card">
                <div class="label">Validations</div>
                <div class="value">{{ $papa->validationsWorkflow->count() }}</div>
            </div>
            <div class="card">
                <div class="label">Code PAPA</div>
                <div class="value">{{ $papa->code }}</div>
            </div>
        </div>
    </div>

    <div class="footer">TB-PAPA CEEAC | Fiche imprimable PAPA</div>
</div>
</body>
</html>
