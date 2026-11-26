<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WinnerController extends Controller
{
    public function store(Project $project, Bid $bid)
    {
        // Hanya untuk simulasi lokal sebelum ada autentikasi.
        abort_unless(app()->environment('local'), 403);

        // Penawaran harus berasal dari proyek yang dipilih.
        abort_unless($bid->project_id === $project->id, 404);

        DB::transaction(function () use ($project, $bid) {

            // Kunci proyek selama proses pemilihan.
            $proyek = Project::query()
                ->lockForUpdate()
                ->findOrFail($project->id);

            if (
                $proyek->status !== 'terbuka' ||
                $proyek->bids()->where('is_winner', true)->exists()
            ) {
                throw ValidationException::withMessages([
                    'pemenang' => 'Proyek sudah ditutup atau sudah memiliki pemenang.',
                ]);
            }

            // Tandai penawaran yang dipilih sebagai pemenang.
            $penawaran = $proyek->bids()->findOrFail($bid->id);

            $penawaran->update([
                'is_winner' => true,
            ]);

            // Tutup proyek setelah pemenang dipilih.
            $proyek->update([
                'status' => 'ditutup',
            ]);
        });

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Pemenang lelang berhasil dipilih.');
    }
}
