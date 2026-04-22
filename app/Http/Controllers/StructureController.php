<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Service;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;

class StructureController extends Controller
{
    public function departements(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $departements = $this->scopeDepartements(
            Departement::withCount(['directions', 'actionsPrioritaires'])
                ->orderBy('type')
                ->orderBy('ordre_affichage'),
            $request
        )->get();

        $scopeLabel = $request->user()->scopeLabel();
        $canManageDepartments = $this->canManageDepartments($request);

        return view('admin.structure.departements', compact('departements', 'scopeLabel', 'canManageDepartments'));
    }

    public function departementCreate(Request $request)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->canManageDepartments($request), 403);

        return view('admin.structure.departement_form', [
            'departement' => null,
            'scopeLabel' => $request->user()->scopeLabel(),
        ]);
    }

    public function departementStore(Request $request)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->canManageDepartments($request), 403);

        $data = $request->validate([
            'code' => 'required|string|max:20|unique:departements,code',
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:50',
            'type' => 'required|in:technique,appui,transversal',
            'description' => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif' => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif', true);

        Departement::create($data);

        return redirect()->route('admin.structure.departements')
            ->with('success', 'Departement cree.');
    }

    public function departementEdit(Request $request, Departement $departement)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->canManageDepartment($request, $departement), 403);

        return view('admin.structure.departement_form', [
            'departement' => $departement,
            'scopeLabel' => $request->user()->scopeLabel(),
        ]);
    }

    public function departementUpdate(Request $request, Departement $departement)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->canManageDepartment($request, $departement), 403);

        $data = $request->validate([
            'code' => 'required|string|max:20|unique:departements,code,' . $departement->id,
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:50',
            'type' => 'required|in:technique,appui,transversal',
            'description' => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif' => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif');

        $departement->update($data);

        return redirect()->route('admin.structure.departements')
            ->with('success', 'Departement mis a jour.');
    }

    public function directions(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $query = Direction::with('departement')
            ->withCount(['services', 'activites', 'users'])
            ->orderBy('type_direction')
            ->orderBy('ordre_affichage');

        $query = $this->scopeDirections($query, $request);

        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->departement_id);
        }

        $directions = $query->get();
        $departements = $this->scopeDepartements(
            Departement::actif()->orderBy('libelle'),
            $request
        )->get();
        $scopeLabel = $request->user()->scopeLabel();

        return view('admin.structure.directions', compact('directions', 'departements', 'scopeLabel'));
    }

    public function directionCreate(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $departements = $this->scopeDepartements(
            Departement::actif()->orderBy('type')->orderBy('libelle'),
            $request
        )->get();

        return view('admin.structure.direction_form', [
            'direction' => null,
            'departements' => $departements,
            'scopeLabel' => $request->user()->scopeLabel(),
        ]);
    }

    public function directionStore(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'departement_id' => 'required|exists:departements,id',
            'code' => 'required|string|max:20|unique:directions,code',
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:50',
            'type_direction' => 'required|in:technique,appui',
            'description' => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif' => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif', true);

        abort_unless(
            $this->scopeDepartements(Departement::query()->whereKey($data['departement_id']), $request)->exists(),
            403
        );

        Direction::create($data);

        return redirect()->route('admin.structure.directions')
            ->with('success', 'Direction creee.');
    }

    public function directionEdit(Request $request, Direction $direction)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->scopeDirections(Direction::query()->whereKey($direction->id), $request)->exists(), 403);

        $departements = $this->scopeDepartements(
            Departement::actif()->orderBy('type')->orderBy('libelle'),
            $request
        )->get();

        return view('admin.structure.direction_form', [
            'direction' => $direction,
            'departements' => $departements,
            'scopeLabel' => $request->user()->scopeLabel(),
        ]);
    }

    public function directionUpdate(Request $request, Direction $direction)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->scopeDirections(Direction::query()->whereKey($direction->id), $request)->exists(), 403);

        $data = $request->validate([
            'departement_id' => 'required|exists:departements,id',
            'code' => 'required|string|max:20|unique:directions,code,' . $direction->id,
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:50',
            'type_direction' => 'required|in:technique,appui',
            'description' => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif' => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif');

        abort_unless(
            $this->scopeDepartements(Departement::query()->whereKey($data['departement_id']), $request)->exists(),
            403
        );

        $direction->update($data);

        return redirect()->route('admin.structure.directions')
            ->with('success', 'Direction mise a jour.');
    }

    public function services(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $query = Service::with('direction.departement')
            ->withCount('activites')
            ->orderBy('ordre_affichage');

        $query = $this->scopeServices($query, $request);

        if ($request->filled('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        $services = $query->get();
        $directions = $this->scopeDirections(
            Direction::actif()->with('departement')->orderBy('libelle'),
            $request
        )->get();
        $scopeLabel = $request->user()->scopeLabel();

        return view('admin.structure.services', compact('services', 'directions', 'scopeLabel'));
    }

    public function serviceCreate(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $directions = $this->scopeDirections(
            Direction::actif()->with('departement')->orderBy('libelle'),
            $request
        )->get();

        return view('admin.structure.service_form', [
            'service' => null,
            'directions' => $directions,
            'scopeLabel' => $request->user()->scopeLabel(),
        ]);
    }

    public function serviceStore(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'direction_id' => 'required|exists:directions,id',
            'code' => 'required|string|max:20|unique:services,code',
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif' => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif', true);

        abort_unless(
            $this->scopeDirections(Direction::query()->whereKey($data['direction_id']), $request)->exists(),
            403
        );

        Service::create($data);

        return redirect()->route('admin.structure.services')
            ->with('success', 'Service cree.');
    }

    public function serviceEdit(Request $request, Service $service)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->scopeServices(Service::query()->whereKey($service->id), $request)->exists(), 403);

        $directions = $this->scopeDirections(
            Direction::actif()->with('departement')->orderBy('libelle'),
            $request
        )->get();

        return view('admin.structure.service_form', [
            'service' => $service,
            'directions' => $directions,
            'scopeLabel' => $request->user()->scopeLabel(),
        ]);
    }

    public function serviceUpdate(Request $request, Service $service)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->scopeServices(Service::query()->whereKey($service->id), $request)->exists(), 403);

        $data = $request->validate([
            'direction_id' => 'required|exists:directions,id',
            'code' => 'required|string|max:20|unique:services,code,' . $service->id,
            'libelle' => 'required|string|max:200',
            'libelle_court' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif' => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif');

        abort_unless(
            $this->scopeDirections(Direction::query()->whereKey($data['direction_id']), $request)->exists(),
            403
        );

        $service->update($data);

        return redirect()->route('admin.structure.services')
            ->with('success', 'Service mis a jour.');
    }

    private function scopeDepartements($query, Request $request)
    {
        return app(UserScopeResolver::class)->applyToQuery($query, $request->user(), [
            'departement' => 'id',
            'direction' => null,
            'service' => null,
        ]);
    }

    private function scopeDirections($query, Request $request)
    {
        return app(UserScopeResolver::class)->applyToQuery($query, $request->user(), [
            'departement' => 'departement_id',
            'direction' => 'id',
            'service' => null,
        ]);
    }

    private function scopeServices($query, Request $request)
    {
        return app(UserScopeResolver::class)->applyToQuery($query, $request->user(), [
            'departement' => null,
            'direction' => 'direction_id',
            'service' => 'id',
        ]);
    }

    private function canManageDepartments(Request $request): bool
    {
        return $request->user()->resolveVisibilityScope()->isGlobal;
    }

    private function canManageDepartment(Request $request, Departement $departement): bool
    {
        return $this->canManageDepartments($request)
            && $this->scopeDepartements(Departement::query()->whereKey($departement->id), $request)->exists();
    }
}
