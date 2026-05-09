<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RekapAbsensiTest extends TestCase
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

    // Test 1: Dashboard menampilkan rekap absensi milik karyawan yang login
    public function test_dashboard_menampilkan_rekap_absensi_karyawan()
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

        $response->assertStatus(200);
        $response->assertViewHas('attendances');
    }

    // Test 2: Rekap hanya milik karyawan yang sedang login
    public function test_rekap_hanya_milik_karyawan_yang_login()
    {
        $employee1 = $this->makeEmployee();
        $employee2 = $this->makeEmployee();

        // Absensi employee1
        Attendance::create([
            'employee_id' => $employee1->id,
            'date'        => now()->toDateString(),
            'time'        => '08:00:00',
            'qr_token'    => Str::random(32),
        ]);

        // Absensi employee2
        Attendance::create([
            'employee_id' => $employee2->id,
            'date'        => now()->toDateString(),
            'time'        => '08:30:00',
            'qr_token'    => Str::random(32),
        ]);

        $response = $this->withSession([
            'employee' => [
                'id'       => $employee1->id,
                'name'     => $employee1->name,
                'email'    => $employee1->email,
                'position' => $employee1->position,
            ]
        ])->get('/karyawan/dashboard');

        $attendances = $response->viewData('attendances');

        // Hanya ada 1 data milik employee1
        $this->assertCount(1, $attendances);
        $this->assertEquals($employee1->id, $attendances->first()->employee_id);
    }

    // Test 3: Rekap diurutkan dari terbaru
    public function test_rekap_diurutkan_dari_terbaru()
    {
        $employee = $this->makeEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'date'        => now()->subDays(2)->toDateString(),
            'time'        => '08:00:00',
            'qr_token'    => Str::random(32),
        ]);

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

        $attendances = $response->viewData('attendances');

        // Data terbaru harus di index pertama
        $this->assertEquals(
            now()->toDateString(),
            $attendances->first()->date
        );
    }

    // Test 4: Jika belum ada absensi, rekap kosong
    public function test_rekap_kosong_jika_belum_absen()
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

        $attendances = $response->viewData('attendances');
        $this->assertCount(0, $attendances);
    }
}