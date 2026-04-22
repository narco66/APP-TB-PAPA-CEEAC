<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiche résultat attendu {{ $ra->code }}</title>
    <style>
        :root {
            --text: #1f2937;
            --muted: #6b7280;
            --line: #d1d5db;
            --soft: #f3f4f6;
            --brand: #3730a3;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; color: var(--text); background: white; }
        .page { max-width: 980px; margin: 0 auto; padding: 24px; }
        .toolbar { display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 20px; }
        .button {
            display: inline-block; padding: 10px 14px; border: 1px solid var(--line); border-radius: 8px;
            color: var(--text); text-decoration: none; background: white; font-size: 14px;
        }
        .button.primary { background: var(--brand); border-color: var(--brand); color: white; }
        .header { border-bottom: 2px solid var(--brand); padding-bottom: 16px; margin-bottom: 20px; }
        .title { font-size: 28px; font-weight: 700; margin: 8px 0; }
        .meta, .grid { display: grid; gap: 12px; }
        .meta { grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top: 16px; }
        .grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .card { border: 1px solid var(--line); border-radius: 10px; padding: 14px; background: white; }
        .soft { background: var(--soft); }
        .label { font-size: 12px; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; letter-spacing: .04em; }
        .value { font-size: 15px; font-weight: 600; }
        .section { margin-top: 22px; }
        .section h2 { font-size: 18px; margin: 0 0 10px 0; color: var(--brand); }
        .text { white-space: pre-wrap; line-height: 1.5; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { border: 1px solid var(--line); padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: var(--soft); font-size: 12px; text-transform: uppercase; color: var(--muted); }
        .badges { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .badge { border: 1px solid var(--line); border-radius: 999px; padding: 4px 10px; font-size: 12px; }
        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid var(--line); font-size: 12px; color: var(--muted); }
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
            <a href="{{ route('resultats-attendus.show', $ra) }}" class="button">Retour à la fiche</a>
            <button type="button" onclick="window.print()" class="button primary">Imprimer</button>
        </div>

        <div class="header">
            <div class="label">Fiche résultat attendu</div>
            <div class="title">{{ $ra->code }} - {{ $ra->libelle }}</div>
            <div class="badges">
                <span class="badge">Statut: {{ ucfirst(str_replace('_', ' ', $ra->statut)) }}</span>
                <span class="badge">Type: {{ $ra->libelleTypeResultat() }}</span>
                <span class="badge">Atteinte: {{ number_format($ra->taux_atteinte, 0) }}%</span>
                <span class="badge">Activités: {{ $ra->activites->count() }}</span>
                @if($ra->preuve_requise)
                    <span class="badge">Preuve requise</span>
                @endif
            </div>
            <div class="meta">
                <div><span class="label">Périmètre de données</span><div class="value">{{ $scopeLabel }}</div></div>
                <div><span class="label">Imprimé le</span><div class="value">{{ $printedAt->format('d/m/Y H:i') }}</div></div>
            </div>
        </div>

        <div class="grid">
            <div class="card soft">
                <div class="label">Objectif immédiat</div>
                <div class="value">{{ $ra->objectifImmediats?->code ?? '-' }}</div>
                <div>{{ $ra->objectifImmediats?->libelle }}</div>
            </div>
            <div class="card soft">
                <div class="label">Action prioritaire</div>
                <div class="value">{{ $ra->objectifImmediats?->actionPrioritaire?->code ?? '-' }}</div>
                <div>{{ $ra->objectifImmediats?->actionPrioritaire?->libelle }}</div>
            </div>
            <div class="card soft">
                <div class="label">Responsable</div>
                <div class="value">{{ $ra->responsable?->nomComplet() ?? '-' }}</div>
            </div>
        </div>

        <div class="section">
            <h2>Description</h2>
            <div class="card text">{{ $ra->description ?: 'Aucune description.' }}</div>
        </div>

        @if($ra->type_preuve_attendue)
            <div class="section">
                <h2>Preuve attendue</h2>
                <div class="card text">{{ $ra->type_preuve_attendue }}</div>
            </div>
        @endif

        @if($ra->notes)
            <div class="section">
                <h2>Notes</h2>
                <div class="card text">{{ $ra->notes }}</div>
            </div>
        @endif

        <div class="section">
            <h2>Activités</h2>
            @if($ra->activites->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Direction</th>
                            <th>Statut</th>
                            <th>Réalisation</th>
                            <th>Fin prévue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ra->activites as $act)
                            <tr>
                                <td>{{ $act->code }}</td>
                                <td>{{ $act->libelle }}</td>
                                <td>{{ $act->direction?->libelle ?? '-' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $act->statut)) }}</td>
                                <td>{{ number_format($act->taux_realisation, 0) }}%</td>
                                <td>{{ $act->date_fin_prevue?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="card text">Aucune activité définie.</div>
            @endif
        </div>

        <div class="section">
            <h2>Documents</h2>
            @if($ra->documents->isNotEmpty())
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
                        @foreach($ra->documents as $doc)
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
                <div class="card text">Aucun document joint.</div>
            @endif
        </div>

        <div class="section">
            <h2>Indicateurs</h2>
            @if($ra->indicateurs->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Réalisation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ra->indicateurs as $ind)
                            <tr>
                                <td>{{ $ind->code }}</td>
                                <td>{{ $ind->libelle }}</td>
                                <td>{{ number_format($ind->taux_realisation_courant, 0) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="card text">Aucun indicateur rattaché.</div>
            @endif
        </div>

        <div class="footer">
            TB-PAPA CEEAC | Résultat attendu {{ $ra->code }}
        </div>
    </div>
</body>
</html>
