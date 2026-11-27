<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
        ]);

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
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->pastikanKlien($request);

        return view('projects.form', ['project' => null]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->pastikanKlien($request);
        $this->normalisasiBudget($request);

        $data = $request->validate($this->rules($request));
        $data['client_id'] = $request->user()->id;
        $data['status'] = 'terbuka';

        $project = Project::create($data);

        return redirect()->route('projects.show', $project)
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
    public function edit(Request $request, Project $project)
    {
        $this->pastikanPemilik($request, $project);
        $this->pastikanBelumAdaPemenang($project);

        return view('projects.form', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->pastikanPemilik($request, $project);
        $this->pastikanBelumAdaPemenang($project);
        $this->normalisasiBudget($request);

        $data = $request->validate($this->rules($request, $project));
        $project->update($data);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $this->pastikanPemilik($request, $project);
        $this->pastikanBelumAdaPemenang($project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }

    private function pastikanKlien(Request $request): void
    {
        abort_unless($request->user()?->role === 'klien', 403);
    }

    private function pastikanPemilik(Request $request, Project $project): void
    {
        $this->pastikanKlien($request);

        abort_unless(
            $request->user()->id === $project->client_id,
            403,
            'Proyek ini bukan milik Anda.'
        );
    }

    private function pastikanBelumAdaPemenang(Project $project): void
    {
        abort_if(
            $project->bids()->where('is_winner', true)->exists(),
            403,
            'Proyek yang sudah memiliki pemenang tidak dapat diubah.'
        );
    }

    private function normalisasiBudget(Request $request): void
    {
        foreach (['budget_min', 'budget_max'] as $kolom) {
            $nilai = $request->input($kolom);

            if (is_string($nilai) && preg_match('/^(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+)$/D', $nilai)) {
                $request->merge([$kolom => str_replace('.', '', $nilai)]);
            }
        }
    }

    private function rules(Request $request, ?Project $project = null): array
    {
        return [
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'kategori' => ['required', 'string', 'max:50'],
            'budget_min' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'budget_max' => ['required', 'integer', 'gte:budget_min', 'max:999999999999'],
            'batas_lelang' => [
                'required',
                'date',
                ...($project && $request->input('batas_lelang') === $project->batas_lelang->format('Y-m-d')
                    ? []
                    : ['after_or_equal:today']),
            ],
        ];
    }
}
