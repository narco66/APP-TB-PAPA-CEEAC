<?php

namespace App\Observers;

use App\Models\Activite;
use Illuminate\Support\Facades\Cache;

class ActiviteObserver
{
    /**
     * Invalide le cache Gantt en incrémentant la version globale.
     * Toutes les clés Gantt incluent cette version et deviennent caduques
     * dès qu'une activité est créée, modifiée ou supprimée.
     */
    public function created(Activite $activite): void
    {
        $this->invalidateGanttCache();
    }

    public function updated(Activite $activite): void
    {
        $this->invalidateGanttCache();
    }

    public function deleted(Activite $activite): void
    {
        $this->invalidateGanttCache();
    }

    public function restored(Activite $activite): void
    {
        $this->invalidateGanttCache();
    }

    private function invalidateGanttCache(): void
    {
        $current = Cache::get('gantt.version', 0);
        Cache::put('gantt.version', $current + 1, now()->addDays(7));
    }
}
