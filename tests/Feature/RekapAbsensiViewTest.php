<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RekapAbsensiViewTest extends TestCase
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

    // Test 1: Tabel rekap tampil di dashboard
    public function test_tabel_rekap_tampil_di_dashboard()
    {
        $employee = $this->makeEmployee();

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/karyawan/dashboard');

        $response->assertSee('Tanggal');
        $response->assertSee('Waktu');
        $response->assertSee('Status');
    }

    // Test 2: Data absensi tampil di tabel
    public function test_data_absensi_tampil_di_tabel()
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

        $response->assertSee(now()->toDateString());
        $response->assertSee('Hadir');
    }

    // Test 3: Pesan kosong tampil jika belum ada absensi
    public function test_pesan_kosong_jika_belum_ada_absensi()
    {
        $employee = $this->makeEmployee();

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/karyawan/dashboard');

        $response->assertSee('Belum ada data absensi');
    }
}