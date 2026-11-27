<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KeamananAplikasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatProyek(User $klien, string $deskripsi = 'Deskripsi proyek uji'): Project
    {
        return Project::create([
            'client_id' => $klien->id,
            'judul' => 'PROYEK-PENANDA-PRIVATE',
            'deskripsi' => $deskripsi,
            'kategori' => 'Web',
            'budget_min' => 100000,
            'budget_max' => 200000,
            'batas_lelang' => today()->addDays(7)->toDateString(),
            'status' => 'terbuka',
        ]);
    }

    public function test_registrasi_menyimpan_password_dalam_bentuk_hash(): void
    {
        $password = 'SandiAman123!';

        $this->post(route('register.store'), [
            'nama' => 'Pengguna Aman',
            'email' => 'pengguna-aman@example.test',
            'role' => 'klien',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect(route('dashboard.index'));

        $user = User::where('email', 'pengguna-aman@example.test')->firstOrFail();
        $this->assertNotSame($password, $user->getRawOriginal('password'));
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_salah_dan_payload_sqli_tidak_bisa_masuk(): void
    {
        $user = User::factory()->create([
            'role' => 'klien',
            'password' => Hash::make('SandiAman123!'),
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => "' OR '1'='1",
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('login.store'), [
            'email' => "' OR 1=1 --",
            'password' => 'asal',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'SandiAman123!',
        ])->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_get_login_tidak_mengeluarkan_pengguna_yang_sedang_login(): void
    {
        $user = User::factory()->create();
        // Bug awal hanya muncul saat APP_ENV=local, bukan testing.
        app()->instance('env', 'local');

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_hanya_post_dan_session_berakhir(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/logout')->assertStatus(405);
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_form_penting_menampilkan_csrf_token(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('name="_token"', false);
        $this->get(route('register'))->assertOk()->assertSee('name="_token"', false);

        $klien = User::factory()->create(['role' => 'klien']);
        $this->actingAs($klien)
            ->get(route('projects.create'))
            ->assertOk()
            ->assertSee('name="_token"', false);

        // Laravel mematikan validasi CSRF saat PHPUnit berjalan.
        // Bukti penolakan token kosong/invalid harus diperiksa di aplikasi lokal/ZAP.
    }

    public function test_stored_xss_dari_deskripsi_ditampilkan_sebagai_teks_aman(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $payload = '<img src=x onerror=alert(1)>';
        $project = $this->buatProyek($klien, $payload);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee(e($payload), false)
            ->assertDontSee($payload, false);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'deskripsi' => $payload,
        ]);
    }

    public function test_input_pencarian_sqli_tidak_mengembalikan_semua_proyek(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $this->buatProyek($klien);

        $this->get(route('projects.index', ['cari' => "' OR 1=1 --"]))
            ->assertOk()
            ->assertDontSee('PROYEK-PENANDA-PRIVATE');
    }

    public function test_header_keamanan_dipakai_dan_dashboard_tidak_dicache(): void
    {
        $this->get(route('projects.index'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $user = User::factory()->create(['role' => 'klien']);
        $response = $this->actingAs($user)->get(route('dashboard.klien'));
        $response->assertOk()->assertHeader('Cache-Control');
        $this->assertStringContainsString(
            'no-store',
            (string) $response->headers->get('Cache-Control')
        );
    }
    public function test_csp_ketat_dan_header_error(): void
    {
        $response = $this->get(route('login'))->assertOk();
        $policy = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("script-src 'self'", $policy);
        $this->assertStringNotContainsString('unsafe-inline', $policy);
        $this->assertStringNotContainsString('unsafe-eval', $policy);
        $this->get('/halaman-tidak-ada-uji')
            ->assertNotFound()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy');
    }
}
