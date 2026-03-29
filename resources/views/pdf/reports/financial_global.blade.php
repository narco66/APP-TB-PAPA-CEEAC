<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        .header { border-bottom: 2px solid #0a2157; margin-bottom: 18px; padding-bottom: 12px; }
        .title { color: #0a2157; font-size: 22px; font-weight: bold; }
        .subtitle { color: #4b5563; font-size: 11px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #dbe2ea; padding: 8px; }
        th { background: #eef3ff; color: #0a2157; text-align: left; }
        .totaux { margin-top: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Rapport budgétaire global du PAPA</div>
        <div class="subtitle">{{ $papa->code }} · Généré le {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Source</th>
                <th>Partenaire</th>
                <th>Prévu</th>
                <th>Engagé</th>
                <th>Décaissé</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgets as $budget)
                <tr>
                    <td>{{ $budget->libelleSource() }}</td>
                    <td>{{ $budget->partenaire?->sigle ?? '—' }}</td>
                    <td>{{ number_format($budget->montant_prevu, 0, ',', ' ') }}</td>
                    <td>{{ number_format($budget->montant_engage, 0, ',', ' ') }}</td>
                    <td>{{ number_format($budget->montant_decaisse, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totaux">
        <tbody>
            <tr><th>Total prévu</th><td>{{ number_format($totaux['prevu'], 0, ',', ' ') }} XAF</td></tr>
            <tr><th>Total engagé</th><td>{{ number_format($totaux['engage'], 0, ',', ' ') }} XAF</td></tr>
            <tr><th>Total décaissé</th><td>{{ number_format($totaux['decaisse'], 0, ',', ' ') }} XAF</td></tr>
        </tbody>
    </table>
</body>
</html>
