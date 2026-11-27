<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;

class BidController extends Controller
{
    private function pastikanDeveloper(Request $request): void
    {
        abort_unless(
            $request->user()?->role === 'developer',
            403,
            'Hanya developer yang boleh mengelola penawaran.'
        );
    }

    private function pastikanPemilikBid(Request $request, Project $project, Bid $bid): void
    {
        $this->pastikanDeveloper($request);
        abort_unless($bid->project_id === $project->id, 404);

        abort_unless(
            $bid->developer_id === $request->user()->id,
            403,
            'Penawaran ini bukan milik Anda.'
        );
    }

    private function lelangAktif(Project $project): bool
    {
        return $project->status === 'terbuka'
            && !$project->batas_lelang->isBefore(today());
    }

    public function store(Request $request, Project $project)
    {
        $this->pastikanDeveloper($request);

        if (!$this->lelangAktif($project)) {
            return back()->withErrors(['lelang' => 'Lelang sudah ditutup.']);
        }

        if ($project->bids()->where('developer_id', $request->user()->id)->exists()) {
            return back()->withErrors(['developer' => 'Anda sudah menawar proyek ini.']);
        }

        $data = $request->validate([
            'harga' => ['required', 'integer', 'min:1', 'max:500000000'],
            'durasi' => ['required', 'integer', 'min:1', 'max:365'],
            'proposal' => ['required', 'string', 'max:5000'],
        ]);

        $project->bids()->create([
            ...$data,
            'developer_id' => $request->user()->id,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Penawaran berhasil dikirim.');
    }

    public function edit(Request $request, Project $project, Bid $bid)
    {
        $this->pastikanPemilikBid($request, $project, $bid);
        abort_unless($this->lelangAktif($project) && !$bid->is_winner, 403);

        return view('bids.edit', compact('project', 'bid'));
    }

    public function update(Request $request, Project $project, Bid $bid)
    {
        $this->pastikanPemilikBid($request, $project, $bid);
        abort_unless($this->lelangAktif($project) && !$bid->is_winner, 403);

        $data = $request->validate([
            'harga' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'durasi' => ['required', 'integer', 'min:1', 'max:3650'],
            'proposal' => ['required', 'string', 'max:5000'],
        ]);

        $bid->update($data);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Penawaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Project $project, Bid $bid)
    {
        $this->pastikanPemilikBid($request, $project, $bid);
        abort_unless($this->lelangAktif($project) && !$bid->is_winner, 403);

        $bid->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Penawaran berhasil dihapus.');
    }
}
