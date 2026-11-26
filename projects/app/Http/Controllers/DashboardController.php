<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return match ($request->user()->role) {
            'klien' => redirect()->route('dashboard.klien'),
            'developer' => redirect()->route('dashboard.developer'),
            'admin' => redirect()->route('dashboard.admin'),
            default => abort(403),
        };
    }

    public function klien(Request $request)
    {
        abort_unless($request->user()->role === 'klien', 403);

        $user = $request->user();
        $projects = $user->projects()->withCount('bids')->latest()->paginate(10);

        return view('dashboard.klien', compact('user', 'projects'));
    }

    public function developer(Request $request)
    {
        abort_unless($request->user()->role === 'developer', 403);

        $user = $request->user();
        $bids = $user->bids()->with('project')->latest()->paginate(10);

        return view('dashboard.developer', compact('user', 'bids'));
    }

    public function admin(Request $request)
    {
        abort_unless($request->user()->role === 'admin', 403);

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
