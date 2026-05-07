<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\QrSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ScanQRTest extends TestCase
{
    use DatabaseTransactions;

    // Buat data dummy employee & QR untuk dipakai semua test
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

    // Test 1: Absensi berhasil dengan token valid
    public function test_absensi_berhasil_dengan_token_valid()
    {
        $employee = $this->makeEmployee();
        $qr = $this->makeQr();

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->postJson('/karyawan/absensi', ['token' => $qr->token]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date'        => now()->toDateString(),
        ]);
    }

    // Test 2: Absensi gagal jika token tidak valid
    public function test_absensi_gagal_token_tidak_valid()
    {
        $employee = $this->makeEmployee();

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->postJson('/karyawan/absensi', ['token' => 'token-salah-123']);

        $response->assertJson(['success' => false]);
    }

    // Test 3: Absensi gagal jika sudah absen hari ini
    public function test_absensi_gagal_jika_sudah_absen_hari_ini()
    {
        $employee = $this->makeEmployee();
        $qr = $this->makeQr();

        // Absen pertama
        Attendance::create([
            'employee_id' => $employee->id,
            'date'        => now()->toDateString(),
            'time'        => now()->toTimeString(),
            'qr_token'    => $qr->token,
        ]);

        // Coba absen lagi dengan QR yang sama
        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->postJson('/karyawan/absensi', ['token' => $qr->token]);

        $response->assertJson(['success' => false]);
    }

    // Test 4: Absensi gagal jika token bukan untuk hari ini
    public function test_absensi_gagal_token_bukan_hari_ini()
    {
        $employee = $this->makeEmployee();

        // Buat QR untuk kemarin
        $qrKemarin = QrSession::create([
            'token' => Str::random(32),
            'date'  => now()->subDay()->toDateString(),
        ]);

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->postJson('/karyawan/absensi', ['token' => $qrKemarin->token]);

        $response->assertJson(['success' => false]);
    }

    // Test 5: Tidak bisa absensi tanpa login
    public function test_absensi_gagal_tanpa_login()
    {
        $qr = $this->makeQr();

        $response = $this->postJson('/karyawan/absensi', [
            'token' => $qr->token
        ]);

        $response->assertStatus(302); // redirect ke login
    }
}