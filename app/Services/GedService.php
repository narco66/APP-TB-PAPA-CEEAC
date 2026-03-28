<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GedService
{
    private string $disk;
    private string $basePath;

    public function __construct()
    {
        $this->disk     = config('app.ged_disk', 'local');
        $this->basePath = config('app.ged_path', 'ged');
    }

    /**
     * Déposer un document dans la GED
     */
    public function deposer(
        UploadedFile $fichier,
        User $deposant,
        array $metadata = [],
        mixed $entite = null,
        ?int $versionPrecedenteId = null
    ): Document {
        // Calculer le hash SHA-256 avant stockage
        $hash = hash_file('sha256', $fichier->getRealPath());

        // Construire le chemin de stockage organisé par date/entité
        $sousRepertoire = $this->basePath . '/' . now()->format('Y/m');
        $nomFichier     = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $fichier->getClientOriginalExtension();
        $chemin         = $sousRepertoire . '/' . $nomFichier;

        Storage::disk($this->disk)->putFileAs($sousRepertoire, $fichier, $nomFichier);

        $document = Document::create(array_merge([
            'titre'                => $metadata['titre'] ?? $fichier->getClientOriginalName(),
            'description'          => $metadata['description'] ?? null,
            'reference'            => $metadata['reference'] ?? null,
            'date_document'        => $metadata['date_document'] ?? now()->toDateString(),
            'chemin_fichier'       => $chemin,
            'nom_fichier_original' => $fichier->getClientOriginalName(),
            'extension'            => strtolower($fichier->getClientOriginalExtension()),
            'mime_type'            => $fichier->getMimeType(),
            'taille_octets'        => $fichier->getSize(),
            'version'              => $versionPrecedenteId ? $this->prochainVersion($versionPrecedenteId) : '1.0',
            'version_precedente_id' => $versionPrecedenteId,
            'confidentialite'      => $metadata['confidentialite'] ?? 'interne',
            'statut'               => 'brouillon',
            'depose_par'           => $deposant->id,
            'categorie_id'         => $metadata['categorie_id'] ?? null,
            'hash_sha256'          => $hash,
        ], $entite ? [
            'documentable_type' => get_class($entite),
            'documentable_id'   => $entite->id,
        ] : []));

        return $document;
    }

    /**
     * Archiver un document (rendre immuable)
     */
    public function archiver(Document $document, User $user): void
    {
        $document->update([
            'est_archive' => true,
            'archive_le'  => now(),
            'statut'      => 'archive',
        ]);

        // Log via Spatie ActivityLog
        activity('ged')
            ->performedOn($document)
            ->causedBy($user)
            ->withProperties(['action' => 'archivage'])
            ->log("Document archivé : {$document->titre}");
    }

    /**
     * Valider un document
     */
    public function valider(Document $document, User $validateur): void
    {
        $document->update([
            'statut'     => 'valide',
            'valide_par' => $validateur->id,
            'valide_le'  => now(),
        ]);
    }

    /**
     * Obtenir le chemin complet pour le téléchargement
     */
    public function cheminComplet(Document $document): string
    {
        return Storage::disk($this->disk)->path($document->chemin_fichier);
    }

    /**
     * Vérifier si le fichier existe physiquement
     */
    public function fichierExiste(Document $document): bool
    {
        return Storage::disk($this->disk)->exists($document->chemin_fichier);
    }

    /**
     * Calculer la prochaine version d'un document
     */
    private function prochainVersion(int $versionPrecedenteId): string
    {
        $precedent = Document::find($versionPrecedenteId);
        if (!$precedent) return '1.0';

        $parts = explode('.', $precedent->version);
        $mineur = (int)($parts[1] ?? 0) + 1;
        return $parts[0] . '.' . $mineur;
    }

    /**
     * Exporter un dossier documentaire (liste des documents d'une entité)
     */
    public function listerDocumentsEntite(string $type, int $id): \Illuminate\Database\Eloquent\Collection
    {
        return Document::where('documentable_type', $type)
            ->where('documentable_id', $id)
            ->with(['categorie', 'deposePar'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
