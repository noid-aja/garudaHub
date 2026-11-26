<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WinnerController extends Controller
{
    public function store(Request $request, Project $project, Bid $bid)
    {
        abort_unless(
            $request->user()?->role === 'klien'
                && $request->user()->id === $project->client_id,
            403,
            'Hanya klien pemilik proyek yang boleh memilih pemenang.'
        );

        abort_unless($bid->project_id === $project->id, 404);

        DB::transaction(function () use ($project, $bid) {
            $proyek = Project::query()->lockForUpdate()->findOrFail($project->id);

            if (
                $proyek->status !== 'terbuka'
                || $proyek->bids()->where('is_winner', true)->exists()
            ) {
                throw ValidationException::withMessages([
                    'pemenang' => 'Proyek sudah ditutup atau memiliki pemenang.',
                ]);
            }

            $penawaran = $proyek->bids()->findOrFail($bid->id);
            $penawaran->update(['is_winner' => true]);
            $proyek->update(['status' => 'ditutup']);
        });

        return redirect()->route('projects.show', $project)
            ->with('success', 'Pemenang berhasil dipilih.');
    }
}
