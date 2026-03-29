<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        .header { border-bottom: 2px solid #0a2157; margin-bottom: 18px; padding-bottom: 12px; }
        .title { color: #0a2157; font-size: 22px; font-weight: bold; }
        .subtitle { color: #4b5563; font-size: 11px; margin-top: 4px; }
        .grid { width: 100%; margin-top: 18px; }
        .grid td { width: 50%; vertical-align: top; padding: 8px; }
        .card { border: 1px solid #dbe2ea; border-radius: 8px; padding: 10px; }
        .label { color: #6b7280; font-size: 10px; text-transform: uppercase; }
        .value { color: #0a2157; font-size: 18px; font-weight: bold; margin-top: 6px; }
        table.metrics { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.metrics th, table.metrics td { border: 1px solid #dbe2ea; padding: 8px; }
        table.metrics th { background: #eef3ff; text-align: left; color: #0a2157; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Rapport exécutif global du PAPA</div>
        <div class="subtitle">{{ $papa->code }} · {{ $papa->libelle }} · Généré le {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="grid">
        <tr>
            <td>
                <div class="card">
                    <div class="label">Exécution physique</div>
                    <div class="value">{{ number_format($kpis['taux_execution_physique'], 1, ',', ' ') }} %</div>
                </div>
            </td>
            <td>
                <div class="card">
                    <div class="label">Exécution financière</div>
                    <div class="value">{{ number_format($kpis['taux_execution_financiere'], 1, ',', ' ') }} %</div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="card">
                    <div class="label">Actions prioritaires</div>
                    <div class="value">{{ $kpis['total_actions_prioritaires'] }}</div>
                </div>
            </td>
            <td>
                <div class="card">
                    <div class="label">Activités en retard</div>
                    <div class="value">{{ $kpis['activites_en_retard'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="metrics">
        <thead>
            <tr>
                <th>Indicateur</th>
                <th>Valeur</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Actions en cours</td><td>{{ $kpis['actions_en_cours'] }}</td></tr>
            <tr><td>Actions terminées</td><td>{{ $kpis['actions_terminees'] }}</td></tr>
            <tr><td>Total activités</td><td>{{ $kpis['total_activites'] }}</td></tr>
            <tr><td>Budget total</td><td>{{ number_format($kpis['budget_total'], 0, ',', ' ') }} XAF</td></tr>
            <tr><td>Budget engagé</td><td>{{ number_format($kpis['budget_engage'], 0, ',', ' ') }} XAF</td></tr>
            <tr><td>Budget décaissé</td><td>{{ number_format($kpis['budget_decaisse'], 0, ',', ' ') }} XAF</td></tr>
            <tr><td>Alertes critiques</td><td>{{ $kpis['alertes_critiques'] }}</td></tr>
            <tr><td>Alertes attention</td><td>{{ $kpis['alertes_attention'] }}</td></tr>
        </tbody>
    </table>
</body>
</html>
