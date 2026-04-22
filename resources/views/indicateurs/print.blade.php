<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiche indicateur {{ $indicateur->code }}</title>
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
        .page { max-width: 1100px; margin: 0 auto; padding: 24px; }
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
        .grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
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
            <a href="{{ route('indicateurs.show', $indicateur) }}" class="button">Retour à la fiche</a>
            <button type="button" onclick="window.print()" class="button primary">Imprimer</button>
        </div>

        <div class="header">
            <div class="label">Fiche indicateur</div>
            <div class="title">{{ $indicateur->code }} - {{ $indicateur->libelle }}</div>
            <div class="badges">
                <span class="badge">Type: {{ ucfirst(str_replace('_', ' ', $indicateur->type_indicateur)) }}</span>
                <span class="badge">Réalisation: {{ number_format($indicateur->taux_realisation_courant, 0) }}%</span>
                <span class="badge">Fréquence: {{ ucfirst($indicateur->frequence_collecte ?? '-') }}</span>
                <span class="badge">Unité: {{ $indicateur->unite_mesure ?? '-' }}</span>
            </div>
            <div class="meta">
                <div><span class="label">Périmètre de données</span><div class="value">{{ $scopeLabel }}</div></div>
                <div><span class="label">Imprimé le</span><div class="value">{{ $printedAt->format('d/m/Y H:i') }}</div></div>
            </div>
        </div>

        <div class="grid">
            <div class="card soft">
                <div class="label">Direction</div>
                <div class="value">{{ $indicateur->direction?->libelle ?? '-' }}</div>
            </div>
            <div class="card soft">
                <div class="label">Responsable</div>
                <div class="value">{{ $indicateur->responsable?->nomComplet() ?? '-' }}</div>
            </div>
            <div class="card soft">
                <div class="label">Cible annuelle</div>
                <div class="value">{{ number_format($indicateur->valeur_cible_annuelle ?? 0, 2, ',', ' ') }} {{ $indicateur->unite_mesure }}</div>
            </div>
            <div class="card soft">
                <div class="label">Baseline</div>
                <div class="value">{{ $indicateur->valeur_baseline !== null ? number_format($indicateur->valeur_baseline, 2, ',', ' ') : '-' }}</div>
            </div>
        </div>

        <div class="section">
            <h2>Définition</h2>
            <div class="card text">{{ $indicateur->definition ?: 'Aucune définition.' }}</div>
        </div>

        <div class="section">
            <h2>Méthode et seuils</h2>
            <table>
                <tbody>
                    <tr>
                        <th>Méthode de calcul</th>
                        <td>{{ $indicateur->methode_calcul ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Source des données</th>
                        <td>{{ $indicateur->source_donnees ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Outil de collecte</th>
                        <td>{{ $indicateur->outil_collecte ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Seuil rouge</th>
                        <td>{{ $indicateur->seuil_alerte_rouge ? $indicateur->seuil_alerte_rouge . '%' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Seuil orange</th>
                        <td>{{ $indicateur->seuil_alerte_orange ? $indicateur->seuil_alerte_orange . '%' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Seuil vert</th>
                        <td>{{ $indicateur->seuil_alerte_vert ? $indicateur->seuil_alerte_vert . '%' : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Historique des valeurs</h2>
            @if($indicateur->valeurs->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Cible</th>
                            <th>Réalisé</th>
                            <th>Taux</th>
                            <th>Statut</th>
                            <th>Saisi par</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($indicateur->valeurs as $v)
                            <tr>
                                <td>{{ $v->periode_libelle }}</td>
                                <td>{{ $v->valeur_cible_periode !== null ? number_format($v->valeur_cible_periode, 2, ',', ' ') : '-' }}</td>
                                <td>{{ $v->valeur_realisee !== null ? number_format($v->valeur_realisee, 2, ',', ' ') : '-' }}</td>
                                <td>{{ number_format($v->taux_realisation, 0) }}%</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $v->statut_validation)) }}</td>
                                <td>{{ $v->saisiPar?->nomComplet() ?? '-' }}</td>
                                <td>{{ $v->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @if($v->commentaire)
                                <tr>
                                    <td colspan="7"><strong>Commentaire :</strong> {{ $v->commentaire }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="card text">Aucune valeur saisie.</div>
            @endif
        </div>

        @if($indicateur->notes)
            <div class="section">
                <h2>Notes</h2>
                <div class="card text">{{ $indicateur->notes }}</div>
            </div>
        @endif

        <div class="footer">
            TB-PAPA CEEAC | Indicateur {{ $indicateur->code }}
        </div>
    </div>
</body>
</html>
