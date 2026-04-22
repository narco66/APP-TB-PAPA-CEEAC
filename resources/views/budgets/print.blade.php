<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budget {{ $papa->code }}</title>
    <style>
        :root { --text:#1f2937; --muted:#6b7280; --line:#d1d5db; --soft:#f3f4f6; --brand:#3730a3; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Arial,sans-serif; color:var(--text); background:white; }
        .page { max-width:1180px; margin:0 auto; padding:24px; }
        .toolbar { display:flex; justify-content:flex-end; gap:12px; margin-bottom:20px; }
        .button { display:inline-block; padding:10px 14px; border:1px solid var(--line); border-radius:8px; color:var(--text); text-decoration:none; background:white; font-size:14px; }
        .button.primary { background:var(--brand); border-color:var(--brand); color:white; }
        .header { border-bottom:2px solid var(--brand); padding-bottom:16px; margin-bottom:20px; }
        .title { font-size:28px; font-weight:700; margin:8px 0; }
        .meta, .grid { display:grid; gap:12px; }
        .meta { grid-template-columns:repeat(2, minmax(0,1fr)); margin-top:16px; }
        .grid { grid-template-columns:repeat(4, minmax(0,1fr)); }
        .card { border:1px solid var(--line); border-radius:10px; padding:14px; background:white; }
        .soft { background:var(--soft); }
        .label { font-size:12px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; letter-spacing:.04em; }
        .value { font-size:15px; font-weight:600; }
        .section { margin-top:22px; }
        .section h2 { font-size:18px; margin:0 0 10px 0; color:var(--brand); }
        table { width:100%; border-collapse:collapse; font-size:13px; }
        th, td { border:1px solid var(--line); padding:8px 10px; text-align:left; vertical-align:top; }
        th { background:var(--soft); font-size:12px; text-transform:uppercase; color:var(--muted); }
        .filters { margin-top:10px; font-size:13px; color:var(--muted); }
        .footer { margin-top:24px; padding-top:12px; border-top:1px solid var(--line); font-size:12px; color:var(--muted); }
        @media print { .toolbar{display:none;} .page{max-width:none; padding:0;} a{color:inherit; text-decoration:none;} }
    </style>
</head>
<body>
<div class="page">
    <div class="toolbar">
        <a href="{{ route('budgets.index', $papa) }}" class="button">Retour au budget</a>
        <button type="button" onclick="window.print()" class="button primary">Imprimer</button>
    </div>

    <div class="header">
        <div class="label">Situation budgétaire</div>
        <div class="title">{{ $papa->code }} - {{ $papa->libelle }}</div>
        <div class="meta">
            <div><span class="label">Périmètre de données</span><div class="value">{{ $scopeLabel }}</div></div>
            <div><span class="label">Imprimé le</span><div class="value">{{ $printedAt->format('d/m/Y H:i') }}</div></div>
        </div>
        @if(collect($filters)->filter()->isNotEmpty())
        <div class="filters">
            Filtres appliqués :
            @if(!empty($filters['source_financement'])) source = {{ $filters['source_financement'] }} @endif
            @if(!empty($filters['action_prioritaire_id'])) | action prioritaire = {{ optional($actionsPrioritaires->firstWhere('id', (int) $filters['action_prioritaire_id']))->code ?? $filters['action_prioritaire_id'] }} @endif
            @if(!empty($filters['annee_budgetaire'])) | année = {{ $filters['annee_budgetaire'] }} @endif
        </div>
        @endif
    </div>

    <div class="grid">
        <div class="card soft"><div class="label">Budget prévu</div><div class="value">{{ number_format($totaux['prevu'], 0, ',', ' ') }} XAF</div></div>
        <div class="card soft"><div class="label">Engagé</div><div class="value">{{ number_format($totaux['engage'], 0, ',', ' ') }} XAF</div></div>
        <div class="card soft"><div class="label">Décaissé</div><div class="value">{{ number_format($totaux['decaisse'], 0, ',', ' ') }} XAF</div></div>
        <div class="card soft"><div class="label">Taux d'engagement</div><div class="value">{{ $totaux['prevu'] > 0 ? number_format($totaux['engage'] / $totaux['prevu'] * 100, 1) : 0 }}%</div></div>
    </div>

    <div class="section">
        <h2>Lignes budgétaires</h2>
        @if($budgets->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Libellé ligne</th>
                    <th>Action prioritaire</th>
                    <th>Année</th>
                    <th>Prévu</th>
                    <th>Engagé</th>
                    <th>Décaissé</th>
                    <th>Taux eng.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $budget)
                <tr>
                    <td>{{ $budget->libelleSource() }}</td>
                    <td>{{ $budget->libelle_ligne ?? '-' }}</td>
                    <td>{{ $budget->actionPrioritaire?->code ?? 'PAPA global' }}</td>
                    <td>{{ $budget->annee_budgetaire }}</td>
                    <td>{{ number_format($budget->montant_prevu, 0, ',', ' ') }}</td>
                    <td>{{ number_format($budget->montant_engage, 0, ',', ' ') }}</td>
                    <td>{{ number_format($budget->montant_decaisse, 0, ',', ' ') }}</td>
                    <td>{{ number_format($budget->tauxEngagement(), 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Total</th>
                    <th>{{ number_format($totaux['prevu'], 0, ',', ' ') }}</th>
                    <th>{{ number_format($totaux['engage'], 0, ',', ' ') }}</th>
                    <th>{{ number_format($totaux['decaisse'], 0, ',', ' ') }}</th>
                    <th>{{ $totaux['prevu'] > 0 ? number_format($totaux['engage'] / $totaux['prevu'] * 100, 1) : 0 }}%</th>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="card">Aucune ligne budgétaire à imprimer.</div>
        @endif
    </div>

    <div class="footer">TB-PAPA CEEAC | Budget {{ $papa->code }}</div>
</div>
</body>
</html>
