<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardViewTest extends TestCase
{
    use DatabaseTransactions;

    // Test 1: Halaman dashboard tidak bisa diakses tanpa login
    public function test_dashboard_tidak_bisa_diakses_tanpa_login()
    {
        $response = $this->get('/karyawan/dashboard');
        $response->assertRedirect('/karyawan');
    }

    // Test 2: Halaman dashboard bisa diakses setelah login
    public function test_dashboard_bisa_diakses_setelah_login()
    {
        $employee = Employee::create([
            'name'     => 'Test Karyawan',
            'email'    => 'test@york.com',
            'password' => Hash::make('password123'),
            'position' => 'Kasir',
        ]);

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/karyawan/dashboard');

        $response->assertStatus(200);
    }

    // Test 3: Halaman dashboard menampilkan nama karyawan
    public function test_dashboard_menampilkan_nama_karyawan()
    {
        $employee = Employee::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi2@york.com',
            'password' => Hash::make('password123'),
            'position' => 'Kasir',
        ]);

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/karyawan/dashboard');

        $response->assertSee('Budi Santoso');
    }

    // Test 4: Halaman dashboard mengandung tombol Scan QR
    public function test_dashboard_mengandung_tombol_scan_qr()
    {
        $employee = Employee::create([
            'name'     => 'Test Karyawan',
            'email'    => 'test2@york.com',
            'password' => Hash::make('password123'),
            'position' => 'Kasir',
        ]);

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'email'    => $employee->email,
                'position' => $employee->position,
            ]
        ])->get('/karyawan/dashboard');

        $response->assertSee('Scan QR');
    }
}