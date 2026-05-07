<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\QrSession;

class DashboardController extends Controller
{
    public function index()
    {
        return view('employee.dashboard');
    }

    public function scanQR(\Illuminate\Http\Request $request)
    {
        $token      = $request->input('token');
        $employeeId = session('employee')['id'];
        $today      = now()->toDateString();

        // Cek token valid dan untuk hari ini
        $qr = QrSession::where('token', $token)
            ->where('date', $today)
            ->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau sudah kadaluarsa.',
            ]);
        }

        // Cek apakah karyawan sudah absen hari ini
        $sudahAbsen = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah melakukan absensi hari ini.',
            ]);
        }

        // Simpan data absensi
        Attendance::create([
            'employee_id' => $employeeId,
            'date'        => $today,
            'time'        => now()->toTimeString(),
            'qr_token'    => $token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat! Selamat bekerja.',
        ]);
    }
}