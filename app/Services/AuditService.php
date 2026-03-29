<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditService
{
    public function __construct(private ?NotificationDispatchService $notificationDispatchService = null) {}

    public function enregistrer(
        string $module,
        string $eventType,
        Model $auditable,
        ?User $acteur = null,
        string $action = 'operation',
        ?string $description = null,
        string $niveau = 'info',
        ?array $donneesAvant = null,
        ?array $donneesApres = null,
        ?Papa $papa = null,
        ?Request $request = null
    ): AuditEvent {
        $request ??= request();
        $horodate = now();

        $payload = [
            'module' => $module,
            'event_type' => $eventType,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'papa_id' => $papa?->id ?? $this->resolvePapaId($auditable),
            'acteur_id' => $acteur?->id,
            'action' => $action,
            'description' => $description,
            'niveau' => $niveau,
            'donnees_avant' => $donneesAvant,
            'donnees_apres' => $donneesApres,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'horodate_evenement' => $horodate,
        ];

        $payload['event_uuid'] = (string) Str::uuid();
        $payload['checksum'] = hash('sha256', json_encode([
            'module' => $module,
            'event_type' => $eventType,
            'auditable_type' => $payload['auditable_type'],
            'auditable_id' => $payload['auditable_id'],
            'acteur_id' => $payload['acteur_id'],
            'action' => $action,
            'horodate_evenement' => $horodate->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $event = AuditEvent::create($payload);

        if ($niveau === 'critical' && $this->notificationDispatchService) {
            $this->notificationDispatchService->dispatch(
                eventType: 'audit_critique',
                titre: 'Événement critique détecté',
                message: $description ?: "Un événement critique a été détecté dans le module {$module}.",
                lien: route('admin.audit-events', ['event_type' => $eventType]),
                notifiable: $event,
                context: ['niveau' => $niveau]
            );
        }

        return $event;
    }

    private function resolvePapaId(Model $auditable): ?int
    {
        if ($auditable instanceof Papa) {
            return $auditable->id;
        }

        return $auditable->papa_id ?? null;
    }
}
