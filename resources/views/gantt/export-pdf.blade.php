<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Planning Gantt — CEEAC</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; }

    /* En-tête institutionnel */
    .header {
        background: #4f46e5;
        color: white;
        padding: 12px 16px;
        margin-bottom: 12px;
    }
    .header h1 { font-size: 14px; font-weight: bold; }
    .header p  { font-size: 9px; opacity: .85; margin-top: 2px; }

    /* Métadonnées */
    .meta {
        background: #f1f5f9;
        border-left: 3px solid #4f46e5;
        padding: 6px 10px;
        margin-bottom: 10px;
        font-size: 8px;
        color: #475569;
    }
    .meta span { margin-right: 18px; }

    /* Tableau */
    table { width: 100%; border-collapse: collapse; font-size: 8px; }
    thead th {
        background: #4f46e5;
        color: white;
        padding: 5px 6px;
        text-align: left;
        font-weight: bold;
        white-space: nowrap;
    }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }

    /* Groupes ResultatAttendu */
    .row-group td {
        background: #e0e7ff !important;
        color: #3730a3;
        font-weight: bold;
        font-size: 8.5px;
        padding: 5px 6px;
    }

    /* Statuts */
    .badge {
        display: inline-block;
        padding: 1px 5px;
        border-radius: 9999px;
        font-size: 7.5px;
        font-weight: bold;
        white-space: nowrap;
    }
    .s-planifiee   { background:#dbeafe; color:#1d4ed8; }
    .s-en_cours    { background:#e0e7ff; color:#4338ca; }
    .s-suspendue   { background:#fef3c7; color:#92400e; }
    .s-terminee    { background:#dcfce7; color:#15803d; }
    .s-abandonnee  { background:#fee2e2; color:#991b1b; }
    .s-non_demarree{ background:#f1f5f9; color:#475569; }

    /* Retard */
    .retard { color: #dc2626; font-weight: bold; }

    /* Barre avancement */
    .progress-wrap { width: 60px; background: #e5e7eb; border-radius: 3px; height: 6px; display: inline-block; vertical-align: middle; }
    .progress-bar  { height: 6px; border-radius: 3px; }

    /* Alerte */
    .alerte { color: #d97706; font-weight: bold; }

    /* Pied de page */
    .footer {
        margin-top: 14px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 7.5px;
        color: #94a3b8;
        display: flex;
        justify-content: space-between;
    }
    .confidential {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        color: #92400e;
        padding: 3px 8px;
        border-radius: 3px;
        font-weight: bold;
        font-size: 7.5px;
    }

    /* Saut de page */
    @media print { .page-break { page-break-after: always; } }
</style>
</head>
<body>

{{-- ── En-tête ─────────────────────────────────────────────── --}}
<div class="header">
    <h1>Diagramme Gantt — Planning des Activités</h1>
    <p>Commission de la CEEAC — Plan d'Action Prioritaire Annuel (PAPA)</p>
</div>

{{-- ── Métadonnées ─────────────────────────────────────────── --}}
<div class="meta">
    <span><strong>Périmètre :</strong> {{ $scopeLabel }}</span>
    <span><strong>Filtres :</strong> {{ $filterLabel }}</span>
    <span><strong>Activités :</strong> {{ $totalActivites }}</span>
    <span><strong>Généré par :</strong> {{ $generatedBy }}</span>
    <span><strong>Date :</strong> {{ now()->format('d/m/Y à H:i') }}</span>
    <span class="confidential">DOCUMENT INTERNE — CONFIDENTIEL</span>
</div>

{{-- ── Tableau du planning ──────────────────────────────────── --}}
<table>
    <thead>
        <tr>
            <th style="width:7%">Code</th>
            <th style="width:22%">Libellé</th>
            <th style="width:8%">Statut</th>
            <th style="width:6%">Priorité</th>
            <th style="width:7%">Début prévu</th>
            <th style="width:7%">Fin prévue</th>
            <th style="width:7%">Début réel</th>
            <th style="width:7%">Fin réelle</th>
            <th style="width:7%">Avanc.</th>
            <th style="width:10%">Responsable</th>
            <th style="width:12%">Budget prévu</th>
        </tr>
    </thead>
    <tbody>
    @foreach($tasks as $task)

        @if(!empty($task['is_group']))
        {{-- Ligne de groupe (ResultatAttendu) --}}
        <tr class="row-group">
            <td colspan="11">{{ $task['text'] }}</td>
        </tr>

        @else
        {{-- Ligne activité --}}
        @php
            $pct      = round(($task['progress'] ?? 0) * 100);
            $barColor = $pct >= 75 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
            $statut   = $task['statut'] ?? '';
            $estRetard = !empty($task['est_retard']);
        @endphp
        <tr>
            <td style="font-family: monospace;">{{ $task['code'] ?? '' }}</td>
            <td style="padding-left: {{ !empty($task['parent']) ? '12px' : '4px' }};">
                {{ $task['est_jalon'] ?? false ? '◆ ' : '' }}{{ Str::limit(str_replace('[' . ($task['code'] ?? '') . '] ', '', $task['text'] ?? ''), 60) }}
            </td>
            <td>
                <span class="badge s-{{ $statut }}">
                    {{ ['non_demarree'=>'Non démarrée','planifiee'=>'Planifiée','en_cours'=>'En cours',
                        'suspendue'=>'Suspendue','terminee'=>'Terminée','abandonnee'=>'Abandonnée'][$statut] ?? $statut }}
                </span>
            </td>
            <td>{{ ['critique'=>'Critique','haute'=>'Haute','normale'=>'Normale','basse'=>'Basse'][$task['priorite'] ?? ''] ?? '' }}</td>
            <td>{{ $task['start_date'] ?? '—' }}</td>
            <td class="{{ $estRetard ? 'retard' : '' }}">{{ $task['end_date'] ?? '—' }}</td>
            <td>{{ $task['date_debut_reelle'] ?? '—' }}</td>
            <td>{{ $task['date_fin_reelle'] ?? '—' }}</td>
            <td>
                <div class="progress-wrap">
                    <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                </div>
                <span style="margin-left:4px;">{{ $pct }}%</span>
                @if($estRetard) <span class="retard"> ⚠</span> @endif
            </td>
            <td>{{ $task['responsable'] ?? '—' }}</td>
            <td>
                @if(($task['budget_prevu'] ?? 0) > 0)
                    {{ number_format($task['budget_prevu'], 0, ',', ' ') }} {{ $task['devise'] ?? 'XAF' }}
                    @if(($task['has_alerte'] ?? false))
                        <span class="alerte"> ⚠{{ $task['nb_alertes'] }}</span>
                    @endif
                @else
                    —
                @endif
            </td>
        </tr>
        @endif

    @endforeach
    </tbody>
</table>

{{-- ── Pied de page ─────────────────────────────────────────── --}}
<div class="footer">
    <span>Commission de la CEEAC — Système TB-PAPA — {{ now()->format('d/m/Y') }}</span>
    <span>{{ $totalActivites }} activité(s) | {{ $scopeLabel }}</span>
</div>

</body>
</html>
