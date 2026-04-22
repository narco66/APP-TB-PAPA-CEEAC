<?php

namespace App\Services;

use App\Models\Parametre;
use App\Models\Papa;
use App\Models\Referentiel;
use App\Models\User;
use App\Models\LibelleMetier;
use Illuminate\Support\Facades\Cache;

class ParametreService
{
    const CACHE_TTL = 3600;

    public function get(string $cle, mixed $defaut = null): mixed
    {
        return Cache::remember("parametre.{$cle}", self::CACHE_TTL, function () use ($cle, $defaut) {
            $p = Parametre::where('cle', $cle)->first();
            if (!$p) {
                return $defaut;
            }
            return $this->cast($p->valeur ?? $p->valeur_defaut, $p->type);
        });
    }

    public function set(string $cle, mixed $valeur, ?User $acteur = null): void
    {
        $p = Parametre::firstOrNew(['cle' => $cle]);
        $p->valeur      = is_array($valeur) ? json_encode($valeur) : (string) $valeur;
        $p->modifie_par = $acteur?->id;
        $p->save();
        Cache::forget("parametre.{$cle}");
    }

    public function getGroupe(string $groupe): array
    {
        return Parametre::where('groupe', $groupe)
            ->get()
            ->mapWithKeys(fn($p) => [$p->cle => $this->cast($p->valeur ?? $p->valeur_defaut, $p->type)])
            ->toArray();
    }

    public function saveGroupe(string $groupe, array $data, ?User $acteur = null): void
    {
        foreach ($data as $cle => $valeur) {
            $this->set($cle, $valeur, $acteur);
        }
    }

    public function hubStats(?User $user = null): array
    {
        if ($user && ! $user->resolveVisibilityScope()->isGlobal) {
            return [
                'total_parametres' => null,
                'referentiels' => null,
                'referentiels_actifs' => null,
                'libelles_modifies' => null,
                'papa_actif' => Papa::query()
                    ->visibleTo($user)
                    ->where('statut', 'en_execution')
                    ->latest('annee')
                    ->first()?->code,
                'maintenance' => $this->get('app_maintenance', false),
                'is_partial' => true,
            ];
        }

        return [
            'total_parametres' => Parametre::count(),
            'referentiels' => Referentiel::count(),
            'referentiels_actifs' => Referentiel::where('actif', true)->count(),
            'libelles_modifies' => LibelleMetier::whereNotNull('valeur_courante')->count(),
            'papa_actif' => Papa::where('statut', 'en_execution')->latest('annee')->first()?->code,
            'maintenance' => $this->get('app_maintenance', false),
            'is_partial' => false,
        ];
    }

    private function cast(mixed $valeur, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($valeur, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $valeur,
            'json'    => json_decode($valeur, true),
            default   => $valeur,
        };
    }
}
