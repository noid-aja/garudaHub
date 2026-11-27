<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function pastikanAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }

    public function users(Request $request)
    {
        $this->pastikanAdmin($request);

        $users = User::orderBy('id')->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->pastikanAdmin($request);
        abort_if($user->id === $request->user()->id, 403, 'Tidak bisa mengubah role sendiri.');

        $data = $request->validate([
            'role' => ['required', Rule::in(['klien', 'developer', 'admin'])],
        ]);

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', 'Role pengguna berhasil diubah.');
    }

    public function deleteUser(Request $request, User $user)
    {
        $this->pastikanAdmin($request);
        abort_if($user->id === $request->user()->id, 403, 'Tidak bisa menghapus akun sendiri.');

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    public function projects(Request $request)
    {
        $this->pastikanAdmin($request);

        $projects = Project::with('client')->withCount('bids')
            ->latest()->paginate(10);

        return view('admin.projects', compact('projects'));
    }

    public function deleteProject(Request $request, Project $project)
    {
        $this->pastikanAdmin($request);

        $project->delete();

        return redirect()->route('admin.projects')
            ->with('success', 'Proyek berhasil dihapus.');
    }
}
