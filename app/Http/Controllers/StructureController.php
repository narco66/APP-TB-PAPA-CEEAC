<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Service;
use Illuminate\Http\Request;

class StructureController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // DÉPARTEMENTS
    // ══════════════════════════════════════════════════════════

    public function departements()
    {
        $this->authorize('admin.utilisateurs');

        $departements = Departement::withCount(['directions', 'actionsPrioritaires'])
            ->orderBy('type')->orderBy('ordre_affichage')->get();

        return view('admin.structure.departements', compact('departements'));
    }

    public function departementCreate()
    {
        $this->authorize('admin.utilisateurs');
        return view('admin.structure.departement_form', ['departement' => null]);
    }

    public function departementStore(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'code'            => 'required|string|max:20|unique:departements,code',
            'libelle'         => 'required|string|max:200',
            'libelle_court'   => 'nullable|string|max:50',
            'type'            => 'required|in:technique,appui,transversal',
            'description'     => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif'           => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif', true);

        Departement::create($data);

        return redirect()->route('admin.structure.departements')
            ->with('success', 'Département créé.');
    }

    public function departementEdit(Departement $departement)
    {
        $this->authorize('admin.utilisateurs');
        return view('admin.structure.departement_form', compact('departement'));
    }

    public function departementUpdate(Request $request, Departement $departement)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'code'            => 'required|string|max:20|unique:departements,code,' . $departement->id,
            'libelle'         => 'required|string|max:200',
            'libelle_court'   => 'nullable|string|max:50',
            'type'            => 'required|in:technique,appui,transversal',
            'description'     => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif'           => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif');

        $departement->update($data);

        return redirect()->route('admin.structure.departements')
            ->with('success', 'Département mis à jour.');
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTIONS
    // ══════════════════════════════════════════════════════════

    public function directions(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $query = Direction::with('departement')
            ->withCount(['services', 'activites', 'users'])
            ->orderBy('type_direction')->orderBy('ordre_affichage');

        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->departement_id);
        }

        $directions   = $query->get();
        $departements = Departement::actif()->orderBy('libelle')->get();

        return view('admin.structure.directions', compact('directions', 'departements'));
    }

    public function directionCreate()
    {
        $this->authorize('admin.utilisateurs');
        $departements = Departement::actif()->orderBy('type')->orderBy('libelle')->get();
        return view('admin.structure.direction_form', ['direction' => null, 'departements' => $departements]);
    }

    public function directionStore(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'departement_id'  => 'required|exists:departements,id',
            'code'            => 'required|string|max:20|unique:directions,code',
            'libelle'         => 'required|string|max:200',
            'libelle_court'   => 'nullable|string|max:50',
            'type_direction'  => 'required|in:technique,appui',
            'description'     => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif'           => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif', true);

        Direction::create($data);

        return redirect()->route('admin.structure.directions')
            ->with('success', 'Direction créée.');
    }

    public function directionEdit(Direction $direction)
    {
        $this->authorize('admin.utilisateurs');
        $departements = Departement::actif()->orderBy('type')->orderBy('libelle')->get();
        return view('admin.structure.direction_form', compact('direction', 'departements'));
    }

    public function directionUpdate(Request $request, Direction $direction)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'departement_id'  => 'required|exists:departements,id',
            'code'            => 'required|string|max:20|unique:directions,code,' . $direction->id,
            'libelle'         => 'required|string|max:200',
            'libelle_court'   => 'nullable|string|max:50',
            'type_direction'  => 'required|in:technique,appui',
            'description'     => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif'           => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif');

        $direction->update($data);

        return redirect()->route('admin.structure.directions')
            ->with('success', 'Direction mise à jour.');
    }

    // ══════════════════════════════════════════════════════════
    // SERVICES
    // ══════════════════════════════════════════════════════════

    public function services(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $query = Service::with('direction.departement')
            ->withCount('activites')
            ->orderBy('ordre_affichage');

        if ($request->filled('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        $services   = $query->get();
        $directions = Direction::actif()->with('departement')->orderBy('libelle')->get();

        return view('admin.structure.services', compact('services', 'directions'));
    }

    public function serviceCreate()
    {
        $this->authorize('admin.utilisateurs');
        $directions = Direction::actif()->with('departement')->orderBy('libelle')->get();
        return view('admin.structure.service_form', ['service' => null, 'directions' => $directions]);
    }

    public function serviceStore(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'direction_id'    => 'required|exists:directions,id',
            'code'            => 'required|string|max:20|unique:services,code',
            'libelle'         => 'required|string|max:200',
            'libelle_court'   => 'nullable|string|max:50',
            'description'     => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif'           => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif', true);

        Service::create($data);

        return redirect()->route('admin.structure.services')
            ->with('success', 'Service créé.');
    }

    public function serviceEdit(Service $service)
    {
        $this->authorize('admin.utilisateurs');
        $directions = Direction::actif()->with('departement')->orderBy('libelle')->get();
        return view('admin.structure.service_form', compact('service', 'directions'));
    }

    public function serviceUpdate(Request $request, Service $service)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'direction_id'    => 'required|exists:directions,id',
            'code'            => 'required|string|max:20|unique:services,code,' . $service->id,
            'libelle'         => 'required|string|max:200',
            'libelle_court'   => 'nullable|string|max:50',
            'description'     => 'nullable|string|max:1000',
            'ordre_affichage' => 'nullable|integer|min:1',
            'actif'           => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif');

        $service->update($data);

        return redirect()->route('admin.structure.services')
            ->with('success', 'Service mis à jour.');
    }
}
