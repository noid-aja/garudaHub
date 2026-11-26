<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BidController extends Controller
{
    // Memastikan lelang masih menerima penawaran.
    private function lelangAktif(Project $project): bool
    {
        return $project->status === 'terbuka'
            && !$project->batas_lelang->isBefore(today());
    }

    // CREATE: Developer mengirim penawaran.
    public function store(Request $request, Project $project)
    {
        if (!$this->lelangAktif($project)) {
            return back()->withErrors([
                'lelang' => 'Lelang sudah ditutup atau melewati batas waktu.',
            ]);
        }

        $data = $request->validate([
            'developer_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'developer'),
                Rule::unique('bids', 'developer_id')
                    ->where('project_id', $project->id),
            ],
            'harga' => ['required', 'integer', 'min:1'],
            'durasi' => ['required', 'integer', 'min:1'],
            'proposal' => ['required', 'string', 'max:5000'],
        ]);

        $project->bids()->create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Penawaran berhasil dikirim.');
    }

    // Menampilkan form edit penawaran.
    public function edit(Project $project, Bid $bid)
    {
        abort_unless($bid->project_id === $project->id, 404);

        return view('bids.edit', compact('project', 'bid'));
    }

    // UPDATE: Memperbarui penawaran.
    public function update(
        Request $request,
        Project $project,
        Bid $bid
    ) {
        abort_unless($bid->project_id === $project->id, 404);

        if (!$this->lelangAktif($project) || $bid->is_winner) {
            return redirect()
                ->route('projects.show', $project)
                ->withErrors([
                    'lelang' => 'Penawaran tidak dapat diubah.',
                ]);
        }

        $data = $request->validate([
            'harga' => ['required', 'integer', 'min:1'],
            'durasi' => ['required', 'integer', 'min:1'],
            'proposal' => ['required', 'string', 'max:5000'],
        ]);

        $bid->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Penawaran berhasil diperbarui.');
    }

    // DELETE: Menghapus penawaran.
    public function destroy(Project $project, Bid $bid)
    {
        abort_unless($bid->project_id === $project->id, 404);

        if (!$this->lelangAktif($project) || $bid->is_winner) {
            return redirect()
                ->route('projects.show', $project)
                ->withErrors([
                    'lelang' => 'Penawaran tidak dapat dihapus.',
                ]);
        }

        $bid->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Penawaran berhasil dihapus.');
    }
}

