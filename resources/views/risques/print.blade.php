<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registre des risques {{ $papa->code }}</title>
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
        .matrix td, .matrix th { text-align:center; }
        .footer { margin-top:24px; padding-top:12px; border-top:1px solid var(--line); font-size:12px; color:var(--muted); }
        @media print { .toolbar{display:none;} .page{max-width:none; padding:0;} a{color:inherit; text-decoration:none;} }
    </style>
</head>
<body>
<div class="page">
    <div class="toolbar">
        <a href="{{ route('risques.index', $papa) }}" class="button">Retour au registre</a>
        <button type="button" onclick="window.print()" class="button primary">Imprimer</button>
    </div>

    <div class="header">
        <div class="label">Registre des risques</div>
        <div class="title">{{ $papa->code }} - {{ $papa->libelle }}</div>
        <div class="meta">
            <div><span class="label">Périmètre de données</span><div class="value">{{ $scopeLabel }}</div></div>
            <div><span class="label">Imprimé le</span><div class="value">{{ $printedAt->format('d/m/Y H:i') }}</div></div>
        </div>
    </div>

    <div class="grid">
        <div class="card soft"><div class="label">Risques critiques</div><div class="value">{{ $stats['rouge'] }}</div></div>
        <div class="card soft"><div class="label">Risques élevés</div><div class="value">{{ $stats['orange'] }}</div></div>
        <div class="card soft"><div class="label">Risques modérés</div><div class="value">{{ $stats['jaune'] }}</div></div>
        <div class="card soft"><div class="label">Risques faibles</div><div class="value">{{ $stats['vert'] }}</div></div>
    </div>

    <div class="section">
        <h2>Matrice des risques</h2>
        @php
        $labelsProbabilite = [
            'tres_faible' => 'Très faible',
            'faible' => 'Faible',
            'moyenne' => 'Moyenne',
            'elevee' => 'Élevée',
            'tres_elevee' => 'Très élevée',
        ];
        $labelsImpact = [
            'negligeable' => 'Négligeable',
            'mineur' => 'Mineur',
            'modere' => 'Modéré',
            'majeur' => 'Majeur',
            'catastrophique' => 'Catastrophique',
        ];
        @endphp
        <table class="matrix">
            <thead>
                <tr>
                    <th>Probabilité / Impact</th>
                    @foreach($labelsImpact as $labImp)
                    <th>{{ $labImp }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(array_reverse($probabilites) as $prob)
                <tr>
                    <th>{{ $labelsProbabilite[$prob] }}</th>
                    @foreach($impacts as $imp)
                    <td>
                        @forelse($matrice[$prob][$imp] as $r)
                            <div>{{ $r->code }}</div>
                        @empty
                            -
                        @endforelse
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Liste des risques</h2>
        @if($risques->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Niveau</th>
                    <th>Score</th>
                    <th>Statut</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($risques as $risque)
                <tr>
                    <td>{{ $risque->code }}</td>
                    <td>{{ $risque->libelle }}</td>
                    <td>{{ ucfirst($risque->categorie) }}</td>
                    <td>{{ ucfirst($risque->niveau_risque) }}</td>
                    <td>{{ $risque->score_risque }}/25</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $risque->statut)) }}</td>
                    <td>{{ $risque->responsable?->nomComplet() ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="card">Aucun risque identifié.</div>
        @endif
    </div>

    <div class="footer">TB-PAPA CEEAC | Registre des risques {{ $papa->code }}</div>
</div>
</body>
</html>
