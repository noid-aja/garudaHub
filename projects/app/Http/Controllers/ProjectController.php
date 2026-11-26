<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'cari' => ['nullable','string', 'max:100']]);


        $projects = Project::with('client')
            ->withCount('bids')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');

                $query->where(function ($q) use ($cari) {
                    $q->where('judul', 'like', "%{$cari}%")
                      ->orWhere('kategori', 'like', "%{$cari}%");
                });
            })
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.form', [
            'project' => null,
            'clients' => User::where('role', 'klien')
                ->orderBy('nama')
                ->get(),
                ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $project = Project::create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Proyek berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['client', 'bids.developer']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('projects.form', [
            'project' => $project,
            'clients' => User::where('role', 'klien')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate($this->rules());

        $project->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Proyek berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }

     private function rules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'klien'),
            ],
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'kategori' => ['required', 'string', 'max:50'],
            'budget_min' => ['required', 'integer', 'min:0'],
            'budget_max' => ['required', 'integer', 'gte:budget_min'],
            'batas_lelang' => ['required', 'date'],
            'status' => ['required', Rule::in(['terbuka', 'ditutup'])],
        ];
    }
}
