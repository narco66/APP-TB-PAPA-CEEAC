<?php

namespace App\Services;

use App\Models\NotificationApp;
use App\Models\NotificationRule;
use App\Models\User;
use App\Models\WorkflowStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class NotificationDispatchService
{
    public function __construct(private AuthorizationMatrixService $authorizationMatrixService) {}

    public function dispatch(
        string $eventType,
        string $titre,
        string $message,
        ?string $lien = null,
        ?Model $notifiable = null,
        array $context = []
    ): void {
        $rules = NotificationRule::query()
            ->where('actif', true)
            ->where('event_type', $eventType)
            ->get();

        foreach ($rules as $rule) {
            $recipients = $this->resolveRecipients($rule, $context);

            foreach ($recipients as $user) {
                NotificationApp::create([
                    'user_id' => $user->id,
                    'type' => $eventType,
                    'titre' => $rule->template_sujet ?: $titre,
                    'message' => $rule->template_message ?: $message,
                    'lien' => $lien,
                    'icone' => $this->resolveIcon($eventType),
                    'niveau' => $this->resolveLevel($eventType, $context),
                    'notifiable_type' => $notifiable ? ($notifiable->getMorphClass() ?: $notifiable::class) : null,
                    'notifiable_id' => $notifiable?->getKey(),
                ]);
            }
        }
    }

    private function resolveRecipients(NotificationRule $rule, array $context): Collection
    {
        if (isset($context['step']) && $context['step'] instanceof WorkflowStep) {
            $stepUsers = $this->authorizationMatrixService->eligibleUsersForStep($context['step']);
            if ($stepUsers->isNotEmpty()) {
                return $stepUsers;
            }
        }

        if (! empty($context['users']) && $context['users'] instanceof Collection) {
            return $context['users']->filter(fn (User $user) => $user->actif)->values();
        }

        if (! empty($context['users']) && is_array($context['users'])) {
            return collect($context['users'])->filter(fn ($user) => $user instanceof User && $user->actif)->values();
        }

        $query = User::query()->actif();

        if ($rule->role_cible) {
            $query->role($rule->role_cible);
        }

        $users = $query->get();

        if (! $rule->permission_cible) {
            return $users;
        }

        return $users
            ->filter(fn (User $user) => $user->checkPermissionTo($rule->permission_cible, 'web'))
            ->values();
    }

    private function resolveIcon(string $eventType): string
    {
        return match (true) {
            str_contains($eventType, 'workflow') => 'fa-diagram-project',
            str_contains($eventType, 'decision') => 'fa-gavel',
            str_contains($eventType, 'audit') => 'fa-shield-halved',
            default => 'fa-bell',
        };
    }

    private function resolveLevel(string $eventType, array $context): string
    {
        if (($context['niveau'] ?? null) === 'critical' || str_contains($eventType, 'critique')) {
            return 'erreur';
        }

        if (str_contains($eventType, 'rejete')) {
            return 'attention';
        }

        if (str_contains($eventType, 'validee') || str_contains($eventType, 'executee')) {
            return 'succes';
        }

        return 'info';
    }
}
