<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\CategorieDocument;
use App\Models\Document;
use App\Services\GedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        // Rattachement optionnel à une entité
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
            ->with('success', 'Document déposé avec succès.');
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

        // Log du téléchargement
        activity('ged')
            ->performedOn($document)
            ->causedBy(auth()->user())
            ->withProperties(['action' => 'telechargement'])
            ->log("Document téléchargé : {$document->titre}");

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

        return back()->with('success', 'Document validé.');
    }

    public function archiver(Request $request, Document $document)
    {
        $this->authorize('archiver', $document);
        $this->gedService->archiver($document, $request->user());

        return back()->with('success', 'Document archivé (lecture seule).');
    }

    public function destroy(Document $document)
    {
        $this->authorize('supprimer', $document);

        // Ne pas supprimer le fichier physique pour préserver l'intégrité
        // Marquer comme obsolète
        $document->update(['statut' => 'obsolete']);
        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document supprimé.');
    }
}
