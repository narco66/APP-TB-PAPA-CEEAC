<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\CategorieDocument;
use App\Models\Document;
use App\Services\GedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function __construct(private GedService $gedService) {}

    public function index(Request $request)
    {
        $this->authorize('document.voir');

        $query = Document::with(['categorie', 'deposePar', 'documentable'])
            ->orderByDesc('created_at');

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('q')) {
            $query->where('titre', 'like', '%' . $request->q . '%');
        }

        // Masquer les docs strictement confidentiels si pas le droit
        if (!$request->user()->can('document.voir_confidentiel')) {
            $query->where('confidentialite', '!=', 'strictement_confidentiel');
        }

        $documents  = $query->paginate(20)->withQueryString();
        $categories = CategorieDocument::actif()->orderBy('libelle')->get();

        return view('documents.index', compact('documents', 'categories'));
    }

    public function create()
    {
        $this->authorize('document.deposer');
        $categories = CategorieDocument::actif()->orderBy('libelle')->get();
        return view('documents.create', compact('categories'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $metadata = $request->only(['titre', 'description', 'reference', 'date_document', 'categorie_id', 'confidentialite']);

        // Rattachement optionnel ÃƒÆ’Ã‚Â  une entitÃƒÆ’Ã‚Â©
        $entite = null;
        if ($request->filled('documentable_type') && $request->filled('documentable_id')) {
            $classe = $request->documentable_type;
            if (class_exists($classe)) {
                $entite = $classe::find($request->documentable_id);
            }
        }

        $document = $this->gedService->deposer(
            $request->file('fichier'),
            $request->user(),
            $metadata,
            $entite
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Document dÃƒÆ’Ã‚Â©posÃƒÆ’Ã‚Â© avec succÃƒÆ’Ã‚Â¨s.');
    }

    public function show(Document $document)
    {
        $this->authorize('voir', $document);
        $document->load(['categorie', 'deposePar', 'validePar', 'versionPrecedente', 'versions']);
        return view('documents.show', compact('document'));
    }

    public function download(Document $document): StreamedResponse
    {
        $this->authorize('telecharger', $document);

        if (!$this->gedService->fichierExiste($document)) {
            abort(404, 'Fichier introuvable sur le serveur.');
        }

        // Log du tÃƒÆ’Ã‚Â©lÃƒÆ’Ã‚Â©chargement
        activity('ged')
            ->performedOn($document)
            ->causedBy(auth()->user())
            ->withProperties(['action' => 'telechargement'])
            ->log("Document tÃƒÆ’Ã‚Â©lÃƒÆ’Ã‚Â©chargÃƒÆ’Ã‚Â© : {$document->titre}");

        $disk = config('app.ged_disk', 'local');
        return Storage::disk($disk)->download(
            $document->chemin_fichier,
            $document->nom_fichier_original
        );
    }

    public function valider(Request $request, Document $document)
    {
        $this->authorize('valider', $document);
        $this->gedService->valider($document, $request->user());

        return back()->with('success', 'Document validÃƒÆ’Ã‚Â©.');
    }

    public function archiver(Request $request, Document $document)
    {
        $this->authorize('archiver', $document);
        $this->gedService->archiver($document, $request->user());

        return back()->with('success', 'Document archivÃƒÆ’Ã‚Â© (lecture seule).');
    }

    public function destroy(Document $document)
    {
        $this->authorize('supprimer', $document);

        // Ne pas supprimer le fichier physique pour prÃƒÆ’Ã‚Â©server l'intÃƒÆ’Ã‚Â©gritÃƒÆ’Ã‚Â©
        // Marquer comme obsolÃƒÆ’Ã‚Â¨te
        $document->update(['statut' => 'obsolete']);
        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document supprimÃƒÆ’Ã‚Â©.');
    }

    /**
     * M11-F07 ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â Export dossier audit en CSV
     * Extraction de tous les documents correspondant aux filtres (action, pÃƒÆ’Ã‚Â©riode, type)
     */
    public function exportAudit(Request $request): Response
    {
        $this->authorize('document.voir');

        $query = Document::with(['categorie', 'deposePar', 'validePar'])
            ->orderBy('created_at', 'desc');

        if (! $request->user()->can('document.voir_confidentiel')) {
            $query->where('confidentialite', '!=', 'strictement_confidentiel');
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('confidentialite')) {
            $query->where('confidentialite', $request->confidentialite);
        }

        $documents = $query->get();

        $filename = 'audit_ged_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($documents): void {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Reference', 'Titre', 'Categorie', 'Statut', 'Confidentialite',
                'Version', 'Hash SHA256', 'Depose par', 'Date depot',
                'Valide par', 'Date validation', 'Nom fichier', 'Taille (octets)',
            ], ';');

            foreach ($documents as $doc) {
                fputcsv($handle, [
                    $doc->id,
                    $doc->reference,
                    $doc->titre,
                    $doc->categorie?->libelle ?? '-',
                    $doc->statut,
                    $doc->confidentialite,
                    $doc->version,
                    $doc->hash_sha256,
                    $doc->deposePar ? "{$doc->deposePar->prenom} {$doc->deposePar->name}" : '-',
                    $doc->created_at->format('d/m/Y H:i:s'),
                    $doc->validePar ? "{$doc->validePar->prenom} {$doc->validePar->name}" : '-',
                    $doc->valide_le?->format('d/m/Y H:i:s') ?? '-',
                    $doc->nom_fichier_original,
                    $doc->taille_octets,
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
