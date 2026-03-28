<?php

namespace App\Exports;

use App\Models\Papa;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BudgetExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(private Papa $papa) {}

    public function array(): array
    {
        $rows = [];

        foreach ($this->papa->budgets()->with('partenaire')->get() as $b) {
            $rows[] = [
                $b->libelleSource(),
                $b->partenaire?->sigle ?? '—',
                $b->montant_prevu,
                $b->montant_engage,
                $b->montant_decaisse,
                round($b->tauxEngagement(), 2),
                round($b->tauxDecaissement(), 2),
                $b->montant_prevu - $b->montant_engage,
            ];
        }

        // Ligne totaux
        $budgets = $this->papa->budgets;
        $rows[] = [
            'TOTAL',
            '',
            $budgets->sum('montant_prevu'),
            $budgets->sum('montant_engage'),
            $budgets->sum('montant_decaisse'),
            $budgets->sum('montant_prevu') > 0
                ? round($budgets->sum('montant_engage') / $budgets->sum('montant_prevu') * 100, 2)
                : 0,
            $budgets->sum('montant_prevu') > 0
                ? round($budgets->sum('montant_decaisse') / $budgets->sum('montant_prevu') * 100, 2)
                : 0,
            $budgets->sum('montant_prevu') - $budgets->sum('montant_engage'),
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Source financement', 'Partenaire',
            'Prévu (XAF)', 'Engagé (XAF)', 'Décaissé (XAF)',
            'Taux engagement (%)', 'Taux décaissement (%)', 'Reste à engager (XAF)',
        ];
    }

    public function title(): string
    {
        return 'Budget';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        return [
            1         => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']]],
            $lastRow  => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']]],
        ];
    }
}
