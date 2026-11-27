<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AksesDanValidasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatProyek(User $klien): Project
    {
        return Project::create([
            'client_id' => $klien->id,
            'judul' => 'Website untuk pengujian',
            'deskripsi' => 'Proyek uji.',
            'kategori' => 'Web',
            'budget_min' => 100000,
            'budget_max' => 200000,
            'batas_lelang' => today()->addDays(7)->toDateString(),
            'status' => 'terbuka',
        ]);
    }

    public function test_developer_tidak_boleh_membuat_proyek(): void
    {
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($developer)
            ->get(route('projects.create'))
            ->assertForbidden();
    }

    public function test_tanggal_lelang_yang_lewat_ditolak(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);

        $this->actingAs($klien)->post(route('projects.store'), [
            'judul' => 'Proyek lama',
            'deskripsi' => 'Tanggal tidak valid.',
            'kategori' => 'Web',
            'budget_min' => '70.000',
            'budget_max' => '100.000',
            'batas_lelang' => today()->subDay()->toDateString(),
        ])->assertSessionHasErrors('batas_lelang');
    }

    public function test_budget_dengan_titik_disimpan_sebagai_angka(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);

        $this->actingAs($klien)->post(route('projects.store'), [
            'judul' => 'Proyek baru',
            'deskripsi' => 'Budget pakai titik.',
            'kategori' => 'Web',
            'budget_min' => '70.000',
            'budget_max' => '150.000',
            'batas_lelang' => today()->addDays(10)->toDateString(),
        ])->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'judul' => 'Proyek baru',
            'client_id' => $klien->id,
            'budget_min' => 70000,
            'budget_max' => 150000,
        ]);
    }

    public function test_klien_lain_tidak_bisa_memilih_pemenang(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $klienLain = User::factory()->create(['role' => 'klien']);
        $developer = User::factory()->create(['role' => 'developer']);
        $project = $this->buatProyek($klien);
        $bid = Bid::create([
            'project_id' => $project->id,
            'developer_id' => $developer->id,
            'harga' => 120000,
            'durasi' => 10,
            'proposal' => 'Saya bisa mengerjakan proyek ini.',
        ]);

        $this->actingAs($klienLain)
            ->post(route('bids.winner', [$project, $bid]))
            ->assertForbidden();

        $this->assertDatabaseHas('bids', ['id' => $bid->id, 'is_winner' => false]);
    }

    public function test_pemilik_bisa_memilih_pemenang_dan_proyek_terkunci(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $developer = User::factory()->create(['role' => 'developer']);
        $project = $this->buatProyek($klien);
        $bid = Bid::create([
            'project_id' => $project->id,
            'developer_id' => $developer->id,
            'harga' => 120000,
            'durasi' => 10,
            'proposal' => 'Saya bisa mengerjakan proyek ini.',
        ]);

        $this->actingAs($klien)
            ->post(route('bids.winner', [$project, $bid]))
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'ditutup']);
        $this->assertDatabaseHas('bids', ['id' => $bid->id, 'is_winner' => true]);

        $this->actingAs($klien)
            ->get(route('projects.edit', $project))
            ->assertForbidden();

        $this->actingAs($klien)
            ->delete(route('projects.destroy', $project))
            ->assertForbidden();
    }

    public function test_harga_dan_durasi_penawaran_terlalu_besar_ditolak(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $developer = User::factory()->create(['role' => 'developer']);
        $project = $this->buatProyek($klien);

        $this->actingAs($developer)->post(route('bids.store', $project), [
            'harga' => '1000000000000',
            'durasi' => 3651,
            'proposal' => 'Nilai terlalu besar.',
        ])->assertSessionHasErrors(['harga', 'durasi']);
    }
}
