<?php

namespace App\Exports;

use App\Models\Indicateur;
use App\Models\Papa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IndicateursExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(private Papa $papa) {}

    public function collection()
    {
        return Indicateur::with(['direction', 'responsable', 'dernierValeur'])
            ->whereHas('actionPrioritaire', fn($q) => $q->where('papa_id', $this->papa->id))
            ->orWhereHas('objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $this->papa->id))
            ->orWhereHas('resultatAttendu.objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $this->papa->id))
            ->orderBy('code')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Code', 'Libellé', 'Type', 'Unité', 'Baseline',
            'Cible annuelle', 'Taux réalisation (%)', 'Tendance',
            'Niveau alerte', 'Direction', 'Responsable', 'Fréquence',
        ];
    }

    public function map($ind): array
    {
        return [
            $ind->code,
            $ind->libelle,
            ucfirst($ind->type_indicateur),
            $ind->unite_mesure,
            $ind->valeur_baseline,
            $ind->valeur_cible_annuelle,
            $ind->taux_realisation_courant,
            $ind->tendance,
            ucfirst($ind->niveauAlerte()),
            $ind->direction?->sigle,
            $ind->responsable?->nomComplet(),
            ucfirst($ind->frequence_collecte),
        ];
    }

    public function title(): string
    {
        return 'Indicateurs';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']]],
        ];
    }
}
