<?php

namespace App\Support\Security;

class UserVisibilityScope
{
    public function __construct(
        public readonly string $level,
        public readonly array $departementIds = [],
        public readonly array $directionIds = [],
        public readonly array $serviceIds = [],
        public readonly bool $isGlobal = false,
        public readonly bool $isTransversal = false,
    ) {}

    public function toArray(): array
    {
        return [
            'level' => $this->level,
            'departement_ids' => $this->departementIds,
            'direction_ids' => $this->directionIds,
            'service_ids' => $this->serviceIds,
            'is_global' => $this->isGlobal,
            'is_transversal' => $this->isTransversal,
        ];
    }

    public function label(): string
    {
        if ($this->isGlobal) {
            return 'Consolidation institutionnelle';
        }

        if ($this->isTransversal) {
            return 'Donnees transversales autorisees';
        }

        return match ($this->level) {
            'departement' => 'Perimetre de donnees : Departement',
            'direction' => 'Perimetre de donnees : Direction',
            'service' => 'Perimetre de donnees : Service',
            default => 'Perimetre de donnees : Restreint',
        };
    }
}
