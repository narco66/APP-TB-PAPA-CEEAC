<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function voir(User $user, Document $document): bool
    {
        if (!$user->can('document.voir')) return false;

        // Documents strictement confidentiels : droit spécial requis
        if ($document->confidentialite === 'strictement_confidentiel') {
            return $user->can('document.voir_confidentiel');
        }

        // Documents archivés : accessibles à tous ceux qui peuvent voir
        return true;
    }

    public function deposer(User $user): bool
    {
        return $user->can('document.deposer');
    }

    public function modifier(User $user, Document $document): bool
    {
        if (!$user->can('document.modifier')) return false;
        // Un document archivé n'est jamais modifiable
        if ($document->est_archive) return false;
        return true;
    }

    public function supprimer(User $user, Document $document): bool
    {
        if (!$user->can('document.supprimer')) return false;
        // Impossible de supprimer un document archivé
        if ($document->est_archive) return false;
        return true;
    }

    public function valider(User $user, Document $document): bool
    {
        if (!$user->can('document.valider')) return false;
        return $document->statut === 'soumis';
    }

    public function archiver(User $user, Document $document): bool
    {
        if (!$user->can('document.archiver')) return false;
        return !$document->est_archive;
    }

    public function telecharger(User $user, Document $document): bool
    {
        if (!$user->can('document.telecharger')) return false;
        if ($document->confidentialite === 'strictement_confidentiel') {
            return $user->can('document.voir_confidentiel');
        }
        return true;
    }
}
