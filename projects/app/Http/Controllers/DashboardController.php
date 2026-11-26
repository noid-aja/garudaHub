<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Bid;

class DashboardController extends Controller
{
    // Dashboard sementara hanya dapat dibuka di lokal.
    private function hanyaLokal(): void
    {
        abort_unless(app()->environment('local'), 403);
    }

    // Halaman untuk memilih akun simulasi.
    public function index()
    {
        $this->hanyaLokal();

        $clients = User::where('role', 'klien')
            ->orderBy('nama')
            ->get();

        $developers = User::where('role', 'developer')
            ->orderBy('nama')
            ->get();

        return view('dashboard.index', compact(
            'clients',
            'developers'
        ));
    }

    // Dashboard klien
    public function klien(User $user)
    {
        $this->hanyaLokal();

        abort_unless($user->role === 'klien', 404);

        $projects = $user->projects()
            ->withCount('bids')
            ->latest()
            ->get();

        return view('dashboard.klien', compact(
            'user',
            'projects'
        ));
    }

    // Dashboard developer
    public function developer(User $user)
    {
        $this->hanyaLokal();

        abort_unless($user->role === 'developer', 404);

        $bids = $user->bids()
            ->with('project')
            ->latest()
            ->get();

        return view('dashboard.developer', compact(
            'user',
            'bids'
        ));
    }

    // Dashboard admin
    public function admin()
    {
        $this->hanyaLokal();

        $statistik = [
            'users' => User::count(),
            'projects' => Project::count(),
            'bids' => Bid::count(),
            'terbuka' => Project::where('status', 'terbuka')->count(),
            'ditutup' => Project::where('status', 'ditutup')->count(),
        ];

        return view('dashboard.admin', compact('statistik'));
    }
}
