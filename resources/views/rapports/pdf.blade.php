<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.5; }

    /* En-tête */
    .header { background: #3730a3; color: white; padding: 20px 30px; }
    .header-inner { display: table; width: 100%; }
    .header-logo { display: table-cell; width: 80px; vertical-align: middle; }
    .header-logo .sigil { width: 60px; height: 60px; background: white; border-radius: 50%; text-align: center; line-height: 60px; font-size: 22pt; font-weight: bold; color: #3730a3; }
    .header-text { display: table-cell; vertical-align: middle; padding-left: 15px; }
    .header-text h1 { font-size: 14pt; font-weight: bold; }
    .header-text p { font-size: 9pt; opacity: 0.85; margin-top: 3px; }
    .header-meta { display: table-cell; text-align: right; vertical-align: middle; font-size: 8pt; opacity: 0.8; }

    /* Corps */
    .content { padding: 20px 30px; }
    .section { margin-bottom: 20px; }
    .section-title { font-size: 11pt; font-weight: bold; color: #3730a3; border-bottom: 2px solid #e0e7ff; padding-bottom: 4px; margin-bottom: 10px; }

    /* KPIs */
    .kpis-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
    .kpi-cell { display: table-cell; width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; }
    .kpi-value { font-size: 18pt; font-weight: bold; color: #3730a3; }
    .kpi-value.green { color: #059669; }
    .kpi-value.red { color: #dc2626; }
    .kpi-label { font-size: 7.5pt; color: #6b7280; margin-top: 2px; }

    /* Tableau */
    table.data { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
    table.data th { background: #4f46e5; color: white; padding: 6px 8px; text-align: left; }
    table.data td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
    table.data tr:nth-child(even) td { background: #f8fafc; }
    .badge-en-retard { background: #fef2f2; color: #dc2626; padding: 1px 5px; border-radius: 3px; font-size: 7pt; }
    .bar-bg { background: #e5e7eb; border-radius: 4px; height: 6px; }
    .bar-fill { height: 6px; border-radius: 4px; }

    /* Sections narratives */
    .narrative { background: #f9fafb; border-left: 3px solid #6366f1; padding: 10px 14px; margin-bottom: 10px; border-radius: 0 4px 4px 0; }
    .narrative p { font-size: 9pt; color: #374151; white-space: pre-wrap; }

    /* Pied de page */
    .footer { position: fixed; bottom: 15px; left: 30px; right: 30px; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 7.5pt; color: #9ca3af; }
    .footer-inner { display: table; width: 100%; }
    .footer-left { display: table-cell; }
    .footer-right { display: table-cell; text-align: right; }

    .page-break { page-break-before: always; }
</style>
</head>
<body>

<!-- Pied de page fixe -->
<div class="footer">
    <div class="footer-inner">
        <div class="footer-left">Commission de la CEEAC — Document confidentiel</div>
        <div class="footer-right">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>
</div>

<!-- En-tête -->
<div class="header">
    <div class="header-inner">
        <div class="header-logo">
            <div class="sigil">C</div>
        </div>
        <div class="header-text">
            <h1>{{ $rapport->titre }}</h1>
            <p>{{ $rapport->papa?->code }} — Période : {{ $rapport->periode_couverte }}</p>
            <p>Commission de la CEEAC — Secrétariat Général</p>
        </div>
        <div class="header-meta">
            <p>Type : {{ ucfirst($rapport->type_rapport) }}</p>
            <p>Rédigé par : {{ $rapport->redigePar?->nomComplet() }}</p>
            <p>Statut : {{ ucfirst($rapport->statut) }}</p>
        </div>
    </div>
</div>

<div class="content">

    <!-- KPIs synthèse -->
    <div class="section">
        <div class="section-title">Indicateurs clés d'exécution</div>
        <div class="kpis-grid">
            <div class="kpi-cell">
                <div class="kpi-value">{{ $rapport->taux_execution_physique }}%</div>
                <div class="kpi-label">Exécution physique</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value green">{{ $rapport->taux_execution_financiere }}%</div>
                <div class="kpi-label">Exécution financière</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value">{{ $kpis['total_actions_prioritaires'] }}</div>
                <div class="kpi-label">Actions prioritaires</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value {{ $kpis['activites_en_retard'] > 0 ? 'red' : '' }}">
                    {{ $kpis['activites_en_retard'] }}
                </div>
                <div class="kpi-label">Activités en retard</div>
            </div>
        </div>
    </div>

    <!-- Avancement par AP -->
    <div class="section">
        <div class="section-title">Avancement par action prioritaire</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Action prioritaire</th>
                    <th>Qualification</th>
                    <th>Statut</th>
                    <th>Taux réalisation</th>
                    <th>OI</th>
                    <th>RA</th>
                    <th>Activités</th>
                </tr>
            </thead>
            <tbody>
                @foreach($papa->actionsPrioritaires->sortBy('ordre') as $ap)
                <tr>
                    <td style="font-family: monospace; font-size: 8pt;">{{ $ap->code }}</td>
                    <td>{{ $ap->libelle }}</td>
                    <td>{{ ucfirst($ap->qualification) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $ap->statut)) }}</td>
                    <td>
                        <div style="display:table;width:100%;">
                            <div style="display:table-cell;width:40px;font-weight:bold;color:#4f46e5;">{{ number_format($ap->taux_realisation, 0) }}%</div>
                            <div style="display:table-cell;vertical-align:middle;padding-left:4px;">
                                <div class="bar-bg">
                                    <div class="bar-fill" style="width:{{ min(100, $ap->taux_realisation) }}%;background:{{ $ap->taux_realisation >= 75 ? '#22c55e' : ($ap->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:center;">{{ $ap->objectifsImmediat->count() }}</td>
                    <td style="text-align:center;">{{ $ap->objectifsImmediat->sum(fn($oi) => $oi->resultatsAttendus->count()) }}</td>
                    <td style="text-align:center;">{{ $ap->objectifsImmediat->sum(fn($oi) => $oi->resultatsAttendus->sum(fn($ra) => $ra->activites->count())) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Budget -->
    <div class="section">
        <div class="section-title">Situation budgétaire</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Source</th>
                    <th style="text-align:right;">Prévu (M XAF)</th>
                    <th style="text-align:right;">Engagé (M XAF)</th>
                    <th style="text-align:right;">Décaissé (M XAF)</th>
                    <th style="text-align:right;">Taux eng.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($papa->budgets as $b)
                <tr>
                    <td>{{ $b->libelleSource() }}</td>
                    <td style="text-align:right;">{{ number_format($b->montant_prevu / 1000000, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($b->montant_engage / 1000000, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($b->montant_decaisse / 1000000, 2) }}</td>
                    <td style="text-align:right;font-weight:bold;">{{ number_format($b->tauxEngagement(), 1) }}%</td>
                </tr>
                @endforeach
                <tr style="font-weight:bold;background:#eff6ff;">
                    <td>TOTAL</td>
                    <td style="text-align:right;">{{ number_format($papa->budgets->sum('montant_prevu') / 1000000, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($papa->budgets->sum('montant_engage') / 1000000, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($papa->budgets->sum('montant_decaisse') / 1000000, 2) }}</td>
                    <td style="text-align:right;">{{ $papa->budgets->sum('montant_prevu') > 0 ? number_format($papa->budgets->sum('montant_engage') / $papa->budgets->sum('montant_prevu') * 100, 1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Sections narratives -->
    @if($rapport->faits_saillants)
    <div class="section">
        <div class="section-title">Faits saillants</div>
        <div class="narrative"><p>{{ $rapport->faits_saillants }}</p></div>
    </div>
    @endif

    @if($rapport->difficultes_rencontrees)
    <div class="section">
        <div class="section-title">Difficultés rencontrées</div>
        <div class="narrative"><p>{{ $rapport->difficultes_rencontrees }}</p></div>
    </div>
    @endif

    @if($rapport->recommandations)
    <div class="section">
        <div class="section-title">Recommandations</div>
        <div class="narrative"><p>{{ $rapport->recommandations }}</p></div>
    </div>
    @endif

    @if($rapport->perspectives)
    <div class="section">
        <div class="section-title">Perspectives</div>
        <div class="narrative"><p>{{ $rapport->perspectives }}</p></div>
    </div>
    @endif

</div>
</body>
</html>
