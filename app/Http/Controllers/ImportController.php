<?php

namespace App\Http\Controllers;

use App\Services\Import\DryRunException;
use App\Services\Import\RbmImportService;
use App\Services\Import\RbmTemplateGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function __construct(private RbmImportService $importService) {}

    // ── Interface principale ──────────────────────────────────────────

    public function index()
    {
        $this->authorize('admin.importer');

        return view('import.index');
    }

    // ── Téléchargement du modèle Excel ────────────────────────────────

    public function telechargerModele(): StreamedResponse
    {
        $this->authorize('admin.importer');

        $path = (new RbmTemplateGenerator())->generate();

        return response()->streamDownload(function () use ($path) {
            readfile($path);
            @unlink($path);
        }, 'modele-import-rbm-' . date('Ymd') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ── Validation (dry-run) ──────────────────────────────────────────

    public function valider(Request $request)
    {
        $this->authorize('admin.importer');

        $request->validate([
            'fichier' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ]);

        $path = $request->file('fichier')->getRealPath();

        try {
            $result = $this->importService->valider($path);
        } catch (DryRunException) {
            // Attendu : dry-run réussi, transaction annulée
            $result = app(RbmImportService::class)->valider($path);
        } catch (\Throwable $e) {
            return back()->with('error', 'Fichier invalide ou illisible : ' . $e->getMessage());
        }

        $summary = $result->summary();

        return view('import.index', compact('summary'));
    }

    // ── Import réel ───────────────────────────────────────────────────

    public function executer(Request $request)
    {
        $this->authorize('admin.importer');

        $request->validate([
            'fichier' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
            'mode'    => ['required', 'in:strict,souple'],
        ]);

        $path = $request->file('fichier')->store('imports/rbm', 'local');
        $fullPath = storage_path("app/{$path}");
        $mode = $request->input('mode', 'strict');

        try {
            $result = $this->importService->import($fullPath, $mode);
        } catch (\RuntimeException $e) {
            // Mode strict : rollback déclenché par erreurs
            $summary = $this->extractResultFromException($e);
            return view('import.index', ['summary' => $summary, 'rolledBack' => true]);
        } catch (\Throwable $e) {
            Log::error('Import RBM échoué', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur inattendue : ' . $e->getMessage());
        }

        $summary = $result->summary();
        $success = !$result->hasErrors();

        return view('import.index', compact('summary', 'success'));
    }

    private function extractResultFromException(\RuntimeException $e): array
    {
        return [
            'sheets'       => [],
            'total_errors' => 1,
            'errors'       => [$e->getMessage()],
        ];
    }
}
