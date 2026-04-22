<?php

namespace App\Services\Import;

class ImportResult
{
    private array $imported = [];
    private array $errors   = [];
    private array $skipped  = [];

    public function imported(string $sheet): void
    {
        $this->imported[$sheet] = ($this->imported[$sheet] ?? 0) + 1;
    }

    public function skipped(string $sheet, string $reason = ''): void
    {
        $this->skipped[$sheet][] = $reason;
    }

    public function error(string $sheet, int $line, string $message): void
    {
        $this->errors[] = "[{$sheet} L.{$line}] {$message}";
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function summary(): array
    {
        $sheets = array_unique(array_merge(
            array_keys($this->imported),
            array_keys($this->skipped),
        ));

        $rows = [];
        foreach ($sheets as $sheet) {
            $rows[] = [
                'sheet'    => $sheet,
                'imported' => $this->imported[$sheet] ?? 0,
                'skipped'  => count($this->skipped[$sheet] ?? []),
            ];
        }

        return [
            'sheets'       => $rows,
            'total_errors' => count($this->errors),
            'errors'       => $this->errors,
        ];
    }
}
