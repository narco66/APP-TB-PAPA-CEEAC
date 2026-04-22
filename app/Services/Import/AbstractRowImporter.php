<?php

namespace App\Services\Import;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class AbstractRowImporter
{
    abstract protected function sheetName(): string;

    abstract protected function processRow(array $row, int $lineNum, ImportResult $result): void;

    // ── Entrée principale ─────────────────────────────────────────────

    public function import(Spreadsheet $spreadsheet, ImportResult $result): void
    {
        $sheet = $spreadsheet->getSheetByName($this->sheetName());

        if ($sheet === null) {
            // Feuille absente → ignorée silencieusement
            return;
        }

        $rows    = $sheet->toArray(null, true, true, false);
        $headers = array_shift($rows); // Ligne 1 = en-têtes

        if (empty(array_filter($headers))) {
            return;
        }

        $headerMap = $this->buildHeaderMap($headers);
        $lineNum   = 2;

        foreach ($rows as $rawRow) {
            $lineNum++;

            $row = $this->mapRow($rawRow, $headerMap);

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $this->processRow($row, $lineNum, $result);
        }
    }

    // ── Helpers de lecture ────────────────────────────────────────────

    private function buildHeaderMap(array $headers): array
    {
        $map = [];
        foreach ($headers as $idx => $header) {
            if ($header !== null && $header !== '') {
                $key = $this->normalizeHeader((string) $header);
                $map[$key] = $idx;
            }
        }
        return $map;
    }

    private function normalizeHeader(string $header): string
    {
        return strtolower(trim(preg_replace('/\s+/', '_', $header)));
    }

    private function mapRow(array $rawRow, array $headerMap): array
    {
        $row = [];
        foreach ($headerMap as $name => $idx) {
            $row[$name] = isset($rawRow[$idx]) ? trim((string) $rawRow[$idx]) : '';
        }
        return $row;
    }

    private function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, fn($v) => $v !== ''));
    }

    // ── Helpers de validation ─────────────────────────────────────────

    protected function val(array $row, string $col): string
    {
        return $row[$col] ?? '';
    }

    protected function required(array $row, string $col, int $line, ImportResult $result): ?string
    {
        $v = $this->val($row, $col);
        if ($v === '') {
            $result->error($this->sheetName(), $line, "Colonne '{$col}' obligatoire mais vide.");
            return null;
        }
        return $v;
    }

    protected function resolveCode(string $model, string $code, string $label, int $line, ImportResult $result): ?int
    {
        if ($code === '') {
            return null;
        }
        $id = $model::where('code', $code)->value('id');
        if ($id === null) {
            $result->error($this->sheetName(), $line, "{$label} : code '{$code}' introuvable.");
        }
        return $id;
    }

    protected function resolveCodeRequired(string $model, string $code, string $label, int $line, ImportResult $result): ?int
    {
        if ($code === '') {
            $result->error($this->sheetName(), $line, "{$label} : code obligatoire mais vide.");
            return null;
        }
        return $this->resolveCode($model, $code, $label, $line, $result);
    }

    protected function resolveUserByEmail(string $email, string $label, int $line, ImportResult $result, bool $required = false): ?int
    {
        if ($email === '') {
            if ($required) {
                $result->error($this->sheetName(), $line, "{$label} : email obligatoire mais vide.");
            }
            return null;
        }
        $id = \App\Models\User::where('email', $email)->value('id');
        if ($id === null) {
            $result->error($this->sheetName(), $line, "{$label} : utilisateur '{$email}' introuvable.");
        }
        return $id;
    }

    protected function parseDate(string $val): ?string
    {
        if ($val === '') {
            return null;
        }

        // Format JJ/MM/AAAA
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        // Format AAAA-MM-JJ (déjà correct)
        if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $val)) {
            return $val;
        }

        // Numéro série Excel
        if (is_numeric($val)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $val)
                    ->format('Y-m-d');
            } catch (\Throwable) {}
        }

        return null;
    }

    protected function parseBool(string $val, bool $default = false): bool
    {
        return match (strtolower(trim($val))) {
            'oui', 'yes', '1', 'true', 'vrai' => true,
            'non', 'no',  '0', 'false','faux'  => false,
            default                             => $default,
        };
    }

    protected function parseDecimal(string $val): ?float
    {
        if ($val === '') {
            return null;
        }
        $val = str_replace([' ', "\u{202F}", ','], ['', '', '.'], $val);
        return is_numeric($val) ? (float) $val : null;
    }

    protected function parseInt(string $val): ?int
    {
        if ($val === '') {
            return null;
        }
        return is_numeric($val) ? (int) $val : null;
    }

    protected function inList(string $val, array $allowed, string $default = ''): string
    {
        return in_array($val, $allowed, true) ? $val : $default;
    }
}
