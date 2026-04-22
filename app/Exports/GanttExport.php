<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GanttExport implements
    FromArray,
    WithHeadings,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    public function __construct(
        private array  $tasks,
        private string $scopeLabel,
        private array  $filters,
        private string $generatedBy,
    ) {}

    public function array(): array
    {
        $rows = [];

        foreach ($this->tasks as $task) {
            // Ignorer les nœuds de groupe (ResultatAttendu)
            if (!empty($task['is_group'])) {
                continue;
            }

            $budgetPrevu    = $task['budget_prevu']    ?? 0;
            $budgetConsomme = $task['budget_consomme'] ?? 0;
            $budgetPct      = $budgetPrevu > 0
                ? number_format($budgetConsomme / $budgetPrevu * 100, 1) . '%'
                : '—';

            $rows[] = [
                $task['code']              ?? '',
                $task['text']              ?? '',
                $this->labelStatut($task['statut']   ?? ''),
                $this->labelPriorite($task['priorite'] ?? ''),
                $task['start_date']        ?? '—',
                $task['end_date']          ?? '—',
                $task['date_debut_reelle'] ?? '—',
                $task['date_fin_reelle']   ?? '—',
                number_format(($task['progress'] ?? 0) * 100, 0) . '%',
                ($task['est_retard'] ?? false) ? 'OUI' : 'NON',
                $task['responsable']       ?? '—',
                $budgetPrevu    > 0 ? number_format($budgetPrevu,    0, ',', ' ') . ' ' . ($task['devise'] ?? 'XAF') : '—',
                $budgetConsomme > 0 ? number_format($budgetConsomme, 0, ',', ' ') . ' ' . ($task['devise'] ?? 'XAF') : '—',
                $budgetPct,
                ($task['has_alerte']    ?? false) ? $task['nb_alertes']    . ' alerte(s)'   : '—',
                ($task['has_documents'] ?? false) ? $task['nb_documents']  . ' document(s)' : '—',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Code',
            'Libellé',
            'Statut',
            'Priorité',
            'Début prévu',
            'Fin prévue',
            'Début réel',
            'Fin réelle',
            'Avancement',
            'En retard',
            'Responsable',
            'Budget prévu',
            'Budget consommé',
            '% Consommé',
            'Alertes',
            'Documents GED',
        ];
    }

    public function title(): string
    {
        return 'Gantt — Planning';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // En-tête
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Métadonnées en bas du fichier
                $metaRow = $lastRow + 2;
                $sheet->setCellValue("A{$metaRow}", 'Périmètre : ' . $this->scopeLabel);
                $sheet->setCellValue("A" . ($metaRow + 1), 'Filtres : '   . $this->buildFilterLabel());
                $sheet->setCellValue("A" . ($metaRow + 2), 'Généré par : ' . $this->generatedBy . ' — ' . now()->format('d/m/Y H:i'));
                $sheet->setCellValue("A" . ($metaRow + 3), 'Document interne CEEAC — Confidentiel');

                foreach (range($metaRow, $metaRow + 3) as $r) {
                    $sheet->getStyle("A{$r}")->getFont()->setItalic(true)->setSize(8);
                    $sheet->getStyle("A{$r}")->getFont()->getColor()->setRGB('6B7280');
                }

                // Bordure sur toutes les cellules de données
                $lastCol = $sheet->getHighestColumn();
                $sheet->getStyle("A1:{$lastCol}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('E5E7EB');

                // Figer la première ligne
                $sheet->freezePane('A2');

                // Colorer les lignes en retard en rouge clair
                foreach (range(2, $lastRow) as $r) {
                    if ($sheet->getCell("J{$r}")->getValue() === 'OUI') {
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('FEE2E2');
                    }
                }
            },
        ];
    }

    private function buildFilterLabel(): string
    {
        $parts = [];
        if (!empty($this->filters['statut']))       $parts[] = 'Statut=' . $this->labelStatut($this->filters['statut']);
        if (!empty($this->filters['priorite']))     $parts[] = 'Priorité=' . $this->labelPriorite($this->filters['priorite']);
        if (!empty($this->filters['date_from']))    $parts[] = 'Du ' . $this->filters['date_from'];
        if (!empty($this->filters['date_to']))      $parts[] = 'Au ' . $this->filters['date_to'];
        return $parts ? implode(' | ', $parts) : 'Aucun filtre (fenêtre glissante)';
    }

    private function labelStatut(string $s): string
    {
        return [
            'non_demarree' => 'Non démarrée', 'planifiee' => 'Planifiée',
            'en_cours' => 'En cours', 'suspendue' => 'Suspendue',
            'terminee' => 'Terminée', 'abandonnee' => 'Abandonnée',
        ][$s] ?? $s;
    }

    private function labelPriorite(string $p): string
    {
        return ['critique' => 'Critique', 'haute' => 'Haute', 'normale' => 'Normale', 'basse' => 'Basse'][$p] ?? $p;
    }
}
