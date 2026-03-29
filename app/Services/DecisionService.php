<?php

namespace App\Services;

use App\Models\Decision;
use App\Models\DecisionAttachment;
use App\Models\Document;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DecisionService
{
    public function __construct(
        private AuditService $auditService,
        private NotificationDispatchService $notificationDispatchService,
    ) {}

    public function creer(array $data, User $acteur): Decision
    {
        return DB::transaction(function () use ($data, $acteur) {
            $decision = Decision::create(array_merge($data, [
                'reference' => $data['reference'] ?? $this->nextReference(),
                'prise_par' => $data['prise_par'] ?? $acteur->id,
                'statut' => $data['statut'] ?? 'brouillon',
            ]));

            $this->auditService->enregistrer(
                module: 'decision',
                eventType: 'decision_creee',
                auditable: $decision,
                acteur: $acteur,
                action: 'creation',
                description: "Décision {$decision->reference} créée",
                donneesApres: $decision->toArray(),
                papa: $decision->papa
            );

            return $decision;
        });
    }

    public function rattacherDocument(
        Decision $decision,
        Document $document,
        User $acteur,
        string $typePiece = 'justificatif',
        bool $obligatoire = true
    ): DecisionAttachment {
        return DB::transaction(function () use ($decision, $document, $acteur, $typePiece, $obligatoire) {
            $attachment = DecisionAttachment::create([
                'decision_id' => $decision->id,
                'document_id' => $document->id,
                'titre' => $document->titre,
                'type_piece' => $typePiece,
                'version' => $document->version,
                'obligatoire' => $obligatoire,
            ]);

            $this->auditService->enregistrer(
                module: 'decision',
                eventType: 'decision_piece_jointe',
                auditable: $decision,
                acteur: $acteur,
                action: 'piece_jointe',
                description: "Document rattaché à la décision {$decision->reference}",
                donneesApres: ['document_id' => $document->id, 'attachment_id' => $attachment->id],
                papa: $decision->papa
            );

            return $attachment;
        });
    }

    public function valider(Decision $decision, User $acteur, ?string $commentaire = null): Decision
    {
        return DB::transaction(function () use ($decision, $acteur, $commentaire) {
            abort_if($decision->attachments()->where('obligatoire', true)->doesntExist(), 422, 'Aucune pièce justificative obligatoire liée à la décision.');

            $before = $decision->toArray();

            $decision->update([
                'statut' => 'validee',
                'validee_par' => $acteur->id,
                'date_decision' => $decision->date_decision ?? now()->toDateString(),
            ]);

            $this->auditService->enregistrer(
                module: 'decision',
                eventType: 'decision_validee',
                auditable: $decision,
                acteur: $acteur,
                action: 'validation',
                description: $commentaire ?: "Décision {$decision->reference} validée",
                donneesAvant: $before,
                donneesApres: $decision->fresh()->toArray(),
                papa: $decision->papa
            );

            $this->notificationDispatchService->dispatch(
                eventType: 'decision_validee',
                titre: "Décision {$decision->reference} validée",
                message: $commentaire ?: 'Une décision validée est disponible pour prise en compte.',
                lien: route('decisions.show', $decision),
                notifiable: $decision
            );

            return $decision->fresh();
        });
    }

    public function executer(Decision $decision, User $acteur, ?string $commentaire = null): Decision
    {
        return DB::transaction(function () use ($decision, $acteur, $commentaire) {
            $before = $decision->toArray();

            $decision->update([
                'statut' => 'executee',
                'date_effet' => $decision->date_effet ?? now()->toDateString(),
            ]);

            $this->auditService->enregistrer(
                module: 'decision',
                eventType: 'decision_executee',
                auditable: $decision,
                acteur: $acteur,
                action: 'execution',
                description: $commentaire ?: "Décision {$decision->reference} exécutée",
                donneesAvant: $before,
                donneesApres: $decision->fresh()->toArray(),
                papa: $decision->papa
            );

            return $decision->fresh();
        });
    }

    private function nextReference(): string
    {
        return 'DEC-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }
}
