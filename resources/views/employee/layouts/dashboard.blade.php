@extends('employee.layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<h4 class="mb-3">Selamat Datang, <strong>{{ session('employee_name') }}</strong></h4>

{{-- Tombol Scan QR — akan diintegrasikan Minggu 3 --}}
<div class="card mb-4 shadow-sm">
    <div class="card-body text-center">
        <p class="text-muted">Fitur Scan QR akan tersedia di Minggu 3</p>
        <button class="btn btn-success" disabled>📷 Scan QR Absensi</button>
    </div>
</div>

{{-- Tabel Rekap Absensi — akan diisi data dari controller Minggu 4 --}}
<div class="card shadow-sm">
    <div class="card-header fw-bold">Rekap Absensi Saya</div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Belum ada data absensi.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection