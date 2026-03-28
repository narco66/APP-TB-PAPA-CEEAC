<?php

namespace App\Exports;

use App\Models\Activite;
use App\Models\Papa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivitesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    ShouldAutoSize,
    WithStyles
{
    public function __construct(private Papa $papa) {}

    public function collection()
    {
        return Activite::with([
            'direction',
            'service',
            'responsable',
            'resultatAttendu.objectifImmediats.actionPrioritaire',
        ])
        ->whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $this->papa->id))
        ->orderBy('code')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Libellé',
            'Action Prioritaire',
            'Objectif Immédiat',
            'Résultat Attendu',
            'Direction',
            'Service',
            'Responsable',
            'Priorité',
            'Statut',
            'Début prévu',
            'Fin prévue',
            'Début réel',
            'Fin réelle',
            'Taux réalisation (%)',
            'Budget prévu (XAF)',
            'Budget engagé (XAF)',
            'Budget consommé (XAF)',
            'En retard',
        ];
    }

    public function map($activite): array
    {
        return [
            $activite->code,
            $activite->libelle,
            $activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->code,
            $activite->resultatAttendu?->objectifImmediats?->code,
            $activite->resultatAttendu?->code,
            $activite->direction?->sigle,
            $activite->service?->libelle,
            $activite->responsable?->nomComplet(),
            ucfirst($activite->priorite),
            ucfirst(str_replace('_', ' ', $activite->statut)),
            $activite->date_debut_prevue?->format('d/m/Y'),
            $activite->date_fin_prevue?->format('d/m/Y'),
            $activite->date_debut_reelle?->format('d/m/Y'),
            $activite->date_fin_reelle?->format('d/m/Y'),
            $activite->taux_realisation,
            $activite->budget_prevu,
            $activite->budget_engage,
            $activite->budget_consomme,
            $activite->estEnRetard() ? 'OUI' : 'NON',
        ];
    }

    public function title(): string
    {
        return 'Activités ' . $this->papa->code;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
