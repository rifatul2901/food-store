<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\QrSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EndToEndAbsensiTest extends TestCase
{
    use DatabaseTransactions;

    private function makeEmployee()
    {
        return Employee::create([
            'name'     => 'Test Karyawan',
            'email'    => 'test' . Str::random(5) . '@york.com',
            'password' => Hash::make('password123'),
            'position' => 'Kasir',
        ]);
    }

    private function makeQr()
    {
        return QrSession::create([
            'token' => Str::random(32),
            'date'  => now()->toDateString(),
        ]);
    }

    // Test 1: Alur lengkap — login, akses dashboard, absensi
    public function test_alur_lengkap_login_dashboard_absensi()
    {
        $employee = $this->makeEmployee();
        $qr       = $this->makeQr();

        // Step 1: Akses login
        $this->get('/karyawan')->assertStatus(200);

        // Step 2: Login
        $this->post('/karyawan/login', [
            'email'    => $employee->email,
            'password' => 'password123',
        ])->assertRedirect('/karyawan/dashboard');

        // Step 3: Akses dashboard
        $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/karyawan/dashboard')->assertStatus(200);

        // Step 4: Scan QR & absensi
        $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->postJson('/karyawan/absensi', [
            'token' => $qr->token
        ])->assertJson(['success' => true]);

        // Step 5: Pastikan data tersimpan di database
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date'        => now()->toDateString(),
        ]);
    }

    // Test 2: Karyawan tidak bisa akses halaman admin Filament
    public function test_karyawan_tidak_bisa_akses_admin()
    {
        $employee = $this->makeEmployee();

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/admin');

        // Harus redirect ke login admin, bukan masuk
        $response->assertRedirect();
        $response->assertDontSee('Dashboard');
    }

    // Test 3: Setelah logout tidak bisa akses dashboard
    public function test_setelah_logout_tidak_bisa_akses_dashboard()
    {
        $employee = $this->makeEmployee();

        // Logout
        $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->post('/karyawan/logout');

        // Coba akses dashboard setelah logout
        $this->get('/karyawan/dashboard')
            ->assertRedirect('/karyawan');
    }

    // Test 4: Login dengan password salah gagal
    public function test_login_dengan_password_salah_gagal()
    {
        $employee = $this->makeEmployee();

        $response = $this->post('/karyawan/login', [
            'email'    => $employee->email,
            'password' => 'password-salah',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // Test 5: Rekap absensi tampil setelah absen
    public function test_rekap_tampil_setelah_absen()
    {
        $employee = $this->makeEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'date'        => now()->toDateString(),
            'time'        => '08:00:00',
            'qr_token'    => Str::random(32),
        ]);

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/karyawan/dashboard');

        // Sesuaikan format dengan yang ada di view (d-m-Y)
        $response->assertSee(now()->format('d-m-Y'));
        $response->assertSee('Hadir');
    }
}