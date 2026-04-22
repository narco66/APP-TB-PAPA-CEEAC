<?php

namespace App\Services\Import\Importers;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Service;
use App\Models\User;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UtilisateursImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Utilisateurs'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $email  = $this->required($row, 'email', $lineNum, $result);
        $prenom = $this->required($row, 'prenom', $lineNum, $result);
        $name   = $this->required($row, 'name', $lineNum, $result);

        if ($email === null || $prenom === null || $name === null) return;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result->error($this->sheetName(), $lineNum, "Email '{$email}' invalide.");
            return;
        }

        $data = [
            'prenom'    => $prenom,
            'name'      => $name,
            'telephone' => $this->val($row, 'telephone') ?: null,
            'titre'     => $this->val($row, 'titre') ?: null,
            'fonction'  => $this->val($row, 'fonction') ?: null,
            'actif'     => $this->parseBool($this->val($row, 'actif'), true),
        ];

        if ($this->val($row, 'matricule') !== '') {
            $data['matricule'] = $this->val($row, 'matricule');
        }

        $scopeLevel = $this->val($row, 'scope_level');
        if ($scopeLevel !== '') {
            $data['scope_level'] = $this->inList($scopeLevel, ['global', 'departement', 'direction', 'service'], 'direction');
        }

        foreach ([
            'direction_id'   => [Direction::class,  'code_direction'],
            'departement_id' => [Departement::class, 'code_departement'],
            'service_id'     => [Service::class,     'code_service'],
        ] as $field => [$model, $col]) {
            $code = $this->val($row, $col);
            if ($code !== '') {
                $id = $model::where('code', $code)->value('id');
                if ($id === null) {
                    $result->error($this->sheetName(), $lineNum, "Code {$col} '{$code}' introuvable.");
                    return;
                }
                $data[$field] = $id;
            }
        }

        $isNew = !User::where('email', $email)->exists();
        if ($isNew) {
            $data['password'] = Hash::make(Str::random(16));
        }

        $user = User::updateOrCreate(['email' => $email], $data);

        // Rôles Spatie
        $rolesRaw = $this->val($row, 'roles');
        if ($rolesRaw !== '') {
            $roles = array_filter(array_map('trim', explode('|', $rolesRaw)));
            try {
                $user->syncRoles($roles);
            } catch (\Throwable $e) {
                $result->error($this->sheetName(), $lineNum, "Rôles invalides : {$rolesRaw}.");
            }
        }

        $result->imported($this->sheetName());
    }
}
