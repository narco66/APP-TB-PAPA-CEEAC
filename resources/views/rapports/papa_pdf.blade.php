<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1f2937; line-height: 1.4; }

    /* En-tête */
    .header { background: #1e3a5f; color: white; padding: 14px 24px; }
    .header-inner { display: table; width: 100%; }
    .header-logo { display: table-cell; width: 70px; vertical-align: middle; }
    .header-logo .sigil { width: 50px; height: 50px; background: white; border-radius: 50%; text-align: center; line-height: 50px; font-size: 18pt; font-weight: bold; color: #1e3a5f; }
    .header-text { display: table-cell; vertical-align: middle; padding-left: 12px; }
    .header-text h1 { font-size: 13pt; font-weight: bold; }
    .header-text p { font-size: 8.5pt; opacity: 0.85; margin-top: 2px; }
    .header-meta { display: table-cell; text-align: right; vertical-align: middle; font-size: 8pt; opacity: 0.8; }

    /* Corps */
    .content { padding: 16px 24px; }
    .section { margin-bottom: 16px; }
    .section-title { font-size: 10pt; font-weight: bold; color: #1e3a5f; border-bottom: 2px solid #bfdbfe; padding-bottom: 3px; margin-bottom: 8px; }

    /* KPIs — 6 colonnes en landscape */
    .kpis-grid { display: table; width: 100%; }
    .kpi-cell { display: table-cell; width: 16.66%; padding: 6px 8px; text-align: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; }
    .kpi-value { font-size: 16pt; font-weight: bold; color: #1e3a5f; }
    .kpi-value.green { color: #059669; }
    .kpi-value.red { color: #dc2626; }
    .kpi-value.amber { color: #d97706; }
    .kpi-label { font-size: 7pt; color: #6b7280; margin-top: 2px; }

    /* Tableau AP */
    table.data { width: 100%; border-collapse: collapse; font-size: 8pt; }
    table.data th { background: #1e40af; color: white; padding: 5px 6px; text-align: left; }
    table.data td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    table.data tr:nth-child(even) td { background: #f8fafc; }

    /* Barres de progression */
    .bar-wrap { display: table; width: 100%; }
    .bar-pct  { display: table-cell; width: 32px; font-weight: bold; color: #1e40af; font-size: 8pt; white-space: nowrap; }
    .bar-col  { display: table-cell; vertical-align: middle; padding-left: 4px; }
    .bar-bg   { background: #e5e7eb; border-radius: 3px; height: 5px; }
    .bar-fill { height: 5px; border-radius: 3px; }

    /* Budget — 3 colonnes */
    .budget-cols { display: table; width: 100%; border-spacing: 8px; border-collapse: separate; }
    .budget-col  { display: table-cell; width: 33.33%; vertical-align: top; }
    .budget-card { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 12px; }
    .budget-card .bc-label { font-size: 7.5pt; color: #6b7280; }
    .budget-card .bc-value { font-size: 13pt; font-weight: bold; color: #1e3a5f; margin-top: 2px; }
    .budget-card .bc-sub   { font-size: 7.5pt; color: #059669; margin-top: 3px; }

    /* Alertes */
    .alert-row { display: table; width: 100%; margin-bottom: 4px; }
    .alert-dot  { display: table-cell; width: 10px; vertical-align: middle; }
    .alert-dot span { display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
    .alert-text { display: table-cell; vertical-align: middle; padding-left: 6px; font-size: 8pt; }
    .dot-red    { background: #dc2626; }
    .dot-orange { background: #d97706; }
    .dot-green  { background: #059669; }

    /* Statut badges */
    .badge { padding: 1px 5px; border-radius: 3px; font-size: 7pt; font-weight: bold; }
    .badge-planifie   { background: #e0e7ff; color: #3730a3; }
    .badge-en_cours   { background: #d1fae5; color: #065f46; }
    .badge-termine    { background: #f0fdf4; color: #16a34a; }
    .badge-suspendu   { background: #fef3c7; color: #92400e; }
    .badge-abandonne  { background: #fef2f2; color: #dc2626; }

    /* Pied de page */
    .footer { position: fixed; bottom: 10px; left: 24px; right: 24px; border-top: 1px solid #e5e7eb; padding-top: 4px; font-size: 7pt; color: #9ca3af; }
    .footer-inner { display: table; width: 100%; }
    .footer-left  { display: table-cell; }
    .footer-right { display: table-cell; text-align: right; }

    /* 2 colonnes layout */
    .two-col { display: table; width: 100%; border-spacing: 10px; border-collapse: separate; }
    .col-left  { display: table-cell; width: 60%; vertical-align: top; }
    .col-right { display: table-cell; width: 40%; vertical-align: top; }
</style>
</head>
<body>

<!-- Pied de page fixe -->
<div class="footer">
    <div class="footer-inner">
        <div class="footer-left">Synthèse PAPA — Commission de la CEEAC — Document confidentiel</div>
        <div class="footer-right">Généré le {{ now()->format('d/m/Y à H:i') }} — Page <span class="pagenum"></span></div>
    </div>
</div>

<!-- En-tête -->
<div class="header">
    <div class="header-inner">
        <div class="header-logo">
            <div class="sigil">C</div>
        </div>
        <div class="header-text">
            <h1>Synthèse Exécutive — {{ $papa->code }}</h1>
            <p>{{ $papa->libelle }}</p>
            <p>Période : {{ $papa->annee }} — Commission de la CEEAC, Secrétariat Général</p>
        </div>
        <div class="header-meta">
            <p>Statut : {{ ucfirst(str_replace('_', ' ', $papa->statut)) }}</p>
            <p>Date : {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>
</div>

<div class="content">

    <!-- KPIs exécutifs : 6 indicateurs -->
    <div class="section">
        <div class="section-title">Tableau de bord exécutif</div>
        <div class="kpis-grid">
            <div class="kpi-cell">
                <div class="kpi-value">{{ $kpis['taux_execution_physique'] }}%</div>
                <div class="kpi-label">Exécution physique</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value green">{{ $kpis['taux_execution_financiere'] }}%</div>
                <div class="kpi-label">Exécution financière</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value">{{ $kpis['total_actions_prioritaires'] }}</div>
                <div class="kpi-label">Actions prioritaires</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value">{{ $kpis['total_activites'] }}</div>
                <div class="kpi-label">Activités totales</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value {{ $kpis['activites_en_retard'] > 0 ? 'red' : 'green' }}">
                    {{ $kpis['activites_en_retard'] }}
                </div>
                <div class="kpi-label">Activités en retard</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-value {{ $kpis['alertes_critiques'] > 0 ? 'red' : ($kpis['alertes_attention'] > 0 ? 'amber' : 'green') }}">
                    {{ $kpis['alertes_critiques'] + $kpis['alertes_attention'] }}
                </div>
                <div class="kpi-label">Alertes actives</div>
            </div>
        </div>
    </div>

    <!-- Deux colonnes : AP à gauche, Budget à droite -->
    <div class="two-col">

        <!-- Actions prioritaires -->
        <div class="col-left">
            <div class="section">
                <div class="section-title">Avancement des actions prioritaires</div>
                <table class="data">
                    <thead>
                        <tr>
                            <th style="width:80px;">Code</th>
                            <th>Libellé</th>
                            <th style="width:70px;">Statut</th>
                            <th style="width:120px;">Réalisation</th>
                            <th style="width:30px; text-align:center;">OI</th>
                            <th style="width:40px; text-align:center;">Acti.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($papa->actionsPrioritaires->sortBy('ordre') as $ap)
                        <tr>
                            <td style="font-family: monospace; font-size: 7.5pt;">{{ $ap->code }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($ap->libelle, 55) }}</td>
                            <td>
                                <span class="badge badge-{{ $ap->statut }}">
                                    {{ ucfirst(str_replace('_', ' ', $ap->statut)) }}
                                </span>
                            </td>
                            <td>
                                <div class="bar-wrap">
                                    <div class="bar-pct">{{ number_format($ap->taux_realisation, 0) }}%</div>
                                    <div class="bar-col">
                                        <div class="bar-bg">
                                            <div class="bar-fill" style="width:{{ min(100, $ap->taux_realisation) }}%;background:{{ $ap->taux_realisation >= 75 ? '#22c55e' : ($ap->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center;">{{ $ap->objectifsImmediat->count() }}</td>
                            <td style="text-align:center;">
                                {{ $ap->objectifsImmediat->sum(fn($oi) => $oi->resultatsAttendus->sum(fn($ra) => $ra->activites->count())) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Budget + alertes -->
        <div class="col-right">

            <!-- Budget synthèse -->
            <div class="section">
                <div class="section-title">Situation financière</div>
                <div class="budget-cols">
                    <div class="budget-col">
                        <div class="budget-card">
                            <div class="bc-label">Budget prévu</div>
                            <div class="bc-value">{{ number_format($kpis['budget_total'] / 1000000, 1) }} M</div>
                            <div class="bc-sub">XAF</div>
                        </div>
                    </div>
                    <div class="budget-col">
                        <div class="budget-card">
                            <div class="bc-label">Engagé</div>
                            <div class="bc-value">{{ number_format($kpis['budget_engage'] / 1000000, 1) }} M</div>
                            <div class="bc-sub">{{ $kpis['taux_engagement'] }}% du prévu</div>
                        </div>
                    </div>
                    <div class="budget-col">
                        <div class="budget-card">
                            <div class="bc-label">Décaissé</div>
                            <div class="bc-value">{{ number_format($kpis['budget_decaisse'] / 1000000, 1) }} M</div>
                            <div class="bc-sub">{{ $kpis['taux_decaissement'] }}% du prévu</div>
                        </div>
                    </div>
                </div>

                <!-- Tableau sources budget -->
                <table class="data" style="margin-top: 8px;">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th style="text-align:right;">Prévu (M)</th>
                            <th style="text-align:right;">Engagé (M)</th>
                            <th style="text-align:right;">Taux</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($papa->budgets as $b)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($b->libelleSource(), 30) }}</td>
                            <td style="text-align:right;">{{ number_format($b->montant_prevu / 1000000, 1) }}</td>
                            <td style="text-align:right;">{{ number_format($b->montant_engage / 1000000, 1) }}</td>
                            <td style="text-align:right;font-weight:bold;">{{ number_format($b->tauxEngagement(), 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Alertes actives -->
            @if($kpis['alertes_critiques'] > 0 || $kpis['alertes_attention'] > 0)
            <div class="section">
                <div class="section-title">Points d'attention</div>
                @if($kpis['alertes_critiques'] > 0)
                <div class="alert-row">
                    <div class="alert-dot"><span class="dot-red"></span></div>
                    <div class="alert-text"><strong>{{ $kpis['alertes_critiques'] }} alerte(s) critique(s)</strong> nécessitent une intervention immédiate.</div>
                </div>
                @endif
                @if($kpis['alertes_attention'] > 0)
                <div class="alert-row">
                    <div class="alert-dot"><span class="dot-orange"></span></div>
                    <div class="alert-text"><strong>{{ $kpis['alertes_attention'] }} alerte(s) d'attention</strong> à surveiller.</div>
                </div>
                @endif
                @if($kpis['activites_en_retard'] > 0)
                <div class="alert-row">
                    <div class="alert-dot"><span class="dot-orange"></span></div>
                    <div class="alert-text"><strong>{{ $kpis['activites_en_retard'] }} activité(s) en retard</strong> sur la planification initiale.</div>
                </div>
                @endif
            </div>
            @else
            <div class="section">
                <div class="section-title">Points d'attention</div>
                <div class="alert-row">
                    <div class="alert-dot"><span class="dot-green"></span></div>
                    <div class="alert-text">Aucune alerte critique ou d'attention active.</div>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</body>
</html>
