<?php

namespace App\Exports;

use App\Models\Papa;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PapaExport implements WithMultipleSheets
{
    public function __construct(private Papa $papa) {}

    public function sheets(): array
    {
        return [
            new ActivitesExport($this->papa),
            new IndicateursExport($this->papa),
            new BudgetExport($this->papa),
        ];
    }
}
