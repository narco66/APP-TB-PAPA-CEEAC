<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'documents';

    protected $fillable = [
        'categorie_id', 'documentable_type', 'documentable_id',
        'titre', 'description', 'reference', 'date_document',
        'chemin_fichier', 'nom_fichier_original', 'extension', 'mime_type', 'taille_octets',
        'version', 'version_precedente_id',
        'confidentialite', 'statut',
        'depose_par', 'valide_par', 'valide_le',
        'est_archive', 'archive_le', 'hash_sha256',
    ];

    protected function casts(): array
    {
        return [
            'date_document' => 'date',
            'valide_le' => 'datetime',
            'archive_le' => 'datetime',
            'est_archive' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Document {$this->reference} : {$eventName}");
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(CategorieDocument::class, 'categorie_id');
    }

    public function deposePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'depose_par');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function versionPrecedente(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'version_precedente_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Document::class, 'version_precedente_id');
    }

    public function decisionAttachments(): HasMany
    {
        return $this->hasMany(DecisionAttachment::class);
    }

    public function urlTelechargement(): string
    {
        return route('documents.download', $this->id);
    }

    public function tailleLisible(): string
    {
        $bytes = $this->taille_octets ?? 0;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' Mo';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' Ko';
        }

        return $bytes . ' octets';
    }

    public function estEditable(): bool
    {
        return ! $this->est_archive && $this->statut !== 'archive';
    }

    public function verifierIntegrite(): bool
    {
        if (! $this->hash_sha256) {
            return true;
        }

        $disk = Storage::disk(config('app.ged_disk', 'local'));

        if (! $disk->exists($this->chemin_fichier)) {
            return false;
        }

        return hash_file('sha256', $disk->path($this->chemin_fichier)) === $this->hash_sha256;
    }

    public function couleurConfidentialite(): string
    {
        return match ($this->confidentialite) {
            'public' => 'green',
            'interne' => 'blue',
            'confidentiel' => 'orange',
            'strictement_confidentiel' => 'red',
            default => 'gray',
        };
    }

    public function iconeExtension(): string
    {
        return match (strtolower($this->extension ?? '')) {
            'pdf' => 'PDF',
            'doc', 'docx' => 'DOC',
            'xls', 'xlsx' => 'XLS',
            'ppt', 'pptx' => 'PPT',
            'jpg', 'jpeg', 'png', 'gif' => 'IMG',
            'zip', 'rar' => 'ZIP',
            default => 'FIC',
        };
    }
}
