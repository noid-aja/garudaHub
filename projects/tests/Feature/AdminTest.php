<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_melihat_daftar_pengguna(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Kelola Pengguna');
    }

    public function test_klien_tidak_bisa_mengakses_panel_admin(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);

        $this->actingAs($klien)
            ->get(route('admin.users'))
            ->assertForbidden();
    }

    public function test_developer_tidak_bisa_mengakses_panel_admin(): void
    {
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($developer)
            ->get(route('admin.projects'))
            ->assertForbidden();
    }

    public function test_admin_tidak_bisa_mengubah_role_sendiri(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $admin), ['role' => 'klien'])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_bisa_mengubah_role_pengguna_lain(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'klien']);

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $user), ['role' => 'developer'])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'developer',
        ]);
    }

    public function test_klien_tidak_bisa_mengubah_role_pengguna(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($klien)
            ->patch(route('admin.users.role', $developer), ['role' => 'admin'])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $developer->id,
            'role' => 'developer',
        ]);
    }

    public function test_admin_bisa_menghapus_pengguna_lain(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $klien = User::factory()->create(['role' => 'klien']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $klien))
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseMissing('users', ['id' => $klien->id]);
    }

    public function test_admin_tidak_bisa_menghapus_akun_sendiri(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_bisa_menghapus_proyek(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $klien = User::factory()->create(['role' => 'klien']);
        $project = Project::create([
            'client_id' => $klien->id,
            'judul' => 'Proyek percobaan admin',
            'deskripsi' => 'Proyek untuk pengujian hapus.',
            'kategori' => 'Web',
            'budget_min' => 100000,
            'budget_max' => 200000,
            'batas_lelang' => today()->addDays(7)->toDateString(),
            'status' => 'terbuka',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
