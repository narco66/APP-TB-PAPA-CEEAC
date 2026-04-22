<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Actions prioritaires - Version imprimable</title>
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
        .meta { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:12px; margin-top:16px; }
        .label { font-size:12px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; letter-spacing:.04em; }
        .value { font-size:15px; font-weight:600; }
        .filters { margin-top:16px; padding:12px; background:var(--soft); border:1px solid var(--line); border-radius:10px; font-size:13px; }
        table { width:100%; border-collapse:collapse; font-size:13px; }
        th, td { border:1px solid var(--line); padding:8px 10px; text-align:left; vertical-align:top; }
        th { background:var(--soft); font-size:12px; text-transform:uppercase; color:var(--muted); }
        .footer { margin-top:24px; padding-top:12px; border-top:1px solid var(--line); font-size:12px; color:var(--muted); }
        @media print { .toolbar{display:none;} .page{max-width:none; padding:0;} a{color:inherit; text-decoration:none;} }
    </style>
</head>
<body>
<div class="page">
    <div class="toolbar">
        <a href="{{ route('actions-prioritaires.index', $filters) }}" class="button">Retour à la liste</a>
        <button type="button" onclick="window.print()" class="button primary">Imprimer</button>
    </div>

    <div class="header">
        <div class="label">Plan d'Action Prioritaire Annuel</div>
        <div class="title">Liste des actions prioritaires</div>
        <div class="meta">
            <div><span class="label">Nombre total</span><div class="value">{{ $actions->count() }} action(s)</div></div>
            <div><span class="label">Imprimé le</span><div class="value">{{ $printedAt->format('d/m/Y H:i') }}</div></div>
        </div>
        <div class="filters">
            <strong>Périmètre :</strong> {{ $scopeLabel }}<br>
            <strong>PAPA :</strong> {{ $papaCourante?->code ? $papaCourante->code . ' - ' . $papaCourante->libelle : 'Tous les PAPA' }}<br>
            <strong>Qualification :</strong> {{ $filters['qualification'] ? ucfirst($filters['qualification']) : 'Toutes' }}<br>
            <strong>Statut :</strong> {{ $filters['statut'] ? ucfirst(str_replace('_', ' ', $filters['statut'])) : 'Tous' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Libellé</th>
                <th>PAPA</th>
                <th>Département</th>
                <th>Qualification</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Taux</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actions as $ap)
            <tr>
                <td>{{ $ap->code }}</td>
                <td>{{ $ap->libelle }}</td>
                <td>{{ $ap->papa?->code ?? '-' }}</td>
                <td>{{ $ap->departement?->libelle ?? 'Tous départements' }}</td>
                <td>{{ ucfirst($ap->qualification) }}</td>
                <td>{{ ucfirst($ap->priorite) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $ap->statut)) }}</td>
                <td>{{ number_format($ap->taux_realisation, 0) }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="8">Aucune action prioritaire trouvée.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">TB-PAPA CEEAC | Actions prioritaires</div>
</div>
</body>
</html>
