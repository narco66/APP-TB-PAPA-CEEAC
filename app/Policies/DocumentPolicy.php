<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function voir(User $user, Document $document): bool
    {
        if (! $user->can('document.voir')) {
            return false;
        }

        if ($document->confidentialite === 'strictement_confidentiel' && ! $user->can('document.voir_confidentiel')) {
            return false;
        }

        return $document->canBeAccessedBy($user);
    }

    public function deposer(User $user): bool
    {
        return $user->can('document.deposer');
    }

    public function modifier(User $user, Document $document): bool
    {
        if (! $user->can('document.modifier') || $document->est_archive) {
            return false;
        }

        return $document->canBeAccessedBy($user);
    }

    public function supprimer(User $user, Document $document): bool
    {
        if (! $user->can('document.supprimer') || $document->est_archive) {
            return false;
        }

        return $document->canBeAccessedBy($user);
    }

    public function valider(User $user, Document $document): bool
    {
        if (! $user->can('document.valider')) {
            return false;
        }

        return $document->statut === 'soumis' && $document->canBeAccessedBy($user);
    }

    public function archiver(User $user, Document $document): bool
    {
        if (! $user->can('document.archiver')) {
            return false;
        }

        return ! $document->est_archive && $document->canBeAccessedBy($user);
    }

    public function telecharger(User $user, Document $document): bool
    {
        if (! $user->can('document.telecharger')) {
            return false;
        }

        if ($document->confidentialite === 'strictement_confidentiel' && ! $user->can('document.voir_confidentiel')) {
            return false;
        }

        return $document->canBeAccessedBy($user);
    }
}