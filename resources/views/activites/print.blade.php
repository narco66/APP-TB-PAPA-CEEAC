<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiche activité {{ $activite->code }}</title>
    <style>
        :root {
            --text: #1f2937;
            --muted: #6b7280;
            --line: #d1d5db;
            --soft: #f3f4f6;
            --brand: #3730a3;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: var(--text);
            background: white;
        }
        .page {
            max-width: 980px;
            margin: 0 auto;
            padding: 24px;
        }
        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            background: white;
            font-size: 14px;
        }
        .button.primary {
            background: var(--brand);
            border-color: var(--brand);
            color: white;
        }
        .header {
            border-bottom: 2px solid var(--brand);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 28px;
            font-weight: 700;
            margin: 8px 0;
        }
        .meta, .grid {
            display: grid;
            gap: 12px;
        }
        .meta {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 16px;
        }
        .grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px;
            background: white;
        }
        .soft {
            background: var(--soft);
        }
        .label {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
            letter-spacing: .04em;
        }
        .value {
            font-size: 15px;
            font-weight: 600;
        }
        .section {
            margin-top: 22px;
        }
        .section h2 {
            font-size: 18px;
            margin: 0 0 10px 0;
            color: var(--brand);
        }
        .text {
            white-space: pre-wrap;
            line-height: 1.5;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th, td {
            border: 1px solid var(--line);
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: var(--soft);
            font-size: 12px;
            text-transform: uppercase;
            color: var(--muted);
        }
        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        .badge {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
        }
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid var(--line);
            font-size: 12px;
            color: var(--muted);
        }
        @media print {
            .toolbar { display: none; }
            .page { max-width: none; padding: 0; }
            a { color: inherit; text-decoration: none; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <a href="{{ route('activites.show', $activite) }}" class="button">Retour à la fiche</a>
            <button type="button" onclick="window.print()" class="button primary">Imprimer</button>
        </div>

        <div class="header">
            <div class="label">Fiche activité</div>
            <div class="title">{{ $activite->code }} - {{ $activite->libelle }}</div>
            <div class="badges">
                <span class="badge">Statut: {{ ucfirst(str_replace('_', ' ', $activite->statut)) }}</span>
                <span class="badge">Priorité: {{ ucfirst($activite->priorite) }}</span>
                <span class="badge">Avancement: {{ number_format($activite->taux_realisation, 0) }}%</span>
                @if($activite->estEnRetard())
                    <span class="badge">En retard</span>
                @endif
            </div>
            <div class="meta">
                <div><span class="label">Périmètre de données</span><div class="value">{{ $scopeLabel }}</div></div>
                <div><span class="label">Imprimé le</span><div class="value">{{ $printedAt->format('d/m/Y H:i') }}</div></div>
            </div>
        </div>

        <div class="grid">
            <div class="card soft">
                <div class="label">Début prévu</div>
                <div class="value">{{ $activite->date_debut_prevue?->format('d/m/Y') ?? '-' }}</div>
            </div>
            <div class="card soft">
                <div class="label">Fin prévue</div>
                <div class="value">{{ $activite->date_fin_prevue?->format('d/m/Y') ?? '-' }}</div>
            </div>
            <div class="card soft">
                <div class="label">Direction / Service</div>
                <div class="value">{{ $activite->direction?->libelle ?? '-' }}</div>
                @if($activite->service)
                    <div>{{ $activite->service->libelle }}</div>
                @endif
            </div>
            <div class="card">
                <div class="label">Responsable</div>
                <div class="value">{{ $activite->responsable?->nomComplet() ?? '-' }}</div>
            </div>
            <div class="card">
                <div class="label">Point focal</div>
                <div class="value">{{ $activite->pointFocal?->nomComplet() ?? '-' }}</div>
            </div>
            <div class="card">
                <div class="label">Budget</div>
                <div class="value">{{ number_format($activite->budget_prevu ?? 0, 0, ',', ' ') }} {{ $activite->devise ?? 'XAF' }}</div>
            </div>
        </div>

        <div class="section">
            <h2>Description</h2>
            <div class="card text">{{ $activite->description ?: 'Aucune description.' }}</div>
        </div>

        <div class="section">
            <h2>Rattachement</h2>
            <table>
                <tbody>
                    <tr>
                        <th>PAPA</th>
                        <td>{{ $activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->papa?->code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Action prioritaire</th>
                        <td>{{ $activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->libelle ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Objectif immédiat</th>
                        <td>{{ $activite->resultatAttendu?->objectifImmediats?->libelle ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Résultat attendu</th>
                        <td>{{ $activite->resultatAttendu?->libelle ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Tâches</h2>
            @if($activite->taches->whereNull('parent_tache_id')->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Libellé</th>
                            <th>Assigné à</th>
                            <th>Statut</th>
                            <th>Avancement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activite->taches->whereNull('parent_tache_id') as $tache)
                            <tr>
                                <td>{{ $tache->libelle }}</td>
                                <td>{{ $tache->assignee?->nomComplet() ?? 'Non assigné' }}</td>
                                <td>{{ str_replace('_', ' ', $tache->statut) }}</td>
                                <td>{{ number_format($tache->taux_realisation, 0) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="card text">Aucune tâche définie.</div>
            @endif
        </div>

        <div class="section">
            <h2>Documents</h2>
            @if($activite->documents->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Version</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activite->documents as $doc)
                            <tr>
                                <td>{{ $doc->titre }}</td>
                                <td>{{ $doc->categorie?->libelle ?? '-' }}</td>
                                <td>v{{ $doc->version }}</td>
                                <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="card text">Aucun document attaché.</div>
            @endif
        </div>

        @if($activite->notes)
            <div class="section">
                <h2>Notes</h2>
                <div class="card text">{{ $activite->notes }}</div>
            </div>
        @endif

        <div class="footer">
            TB-PAPA CEEAC | Activité {{ $activite->code }}
        </div>
    </div>
</body>
</html>
