@extends('employee.layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<h4 class="mb-3">
    Selamat Datang, <strong>{{ session('employee')['name'] }}</strong>
    <small class="text-muted fs-6">— {{ session('employee')['position'] }}</small>
</h4>

<div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold">📷 Scan QR Absensi</div>
    <div class="card-body text-center">
        <button class="btn btn-success mb-3" id="btn-scan" onclick="startScan()">
            📷 Scan QR Absensi
        </button>
        <button class="btn btn-secondary mb-3 d-none" id="btn-stop" onclick="stopScan()">
            ✋ Stop Scan
        </button>
        <div id="reader" style="width: 100%; max-width: 400px; margin: auto;"></div>
        <div id="hasil-scan" class="mt-3"></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-bold">📋 Rekap Absensi Saya</div>
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

{{-- Library dan Script langsung di sini --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode = null;

    function startScan() {
        document.getElementById('btn-scan').classList.add('d-none');
        document.getElementById('btn-stop').classList.remove('d-none');
        document.getElementById('hasil-scan').innerHTML = '';

        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "user" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                stopScan();
                kirimAbsensi(decodedText);
            },
            (error) => {}
        ).catch(err => {
            document.getElementById('hasil-scan').innerHTML =
                '<div class="alert alert-danger">Tidak bisa akses kamera: ' + err + '</div>';
        });
    }

    function stopScan() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                document.getElementById('reader').innerHTML = '';
                document.getElementById('btn-scan').classList.remove('d-none');
                document.getElementById('btn-stop').classList.add('d-none');
            });
        }
    }

    function kirimAbsensi(token) {
        document.getElementById('hasil-scan').innerHTML =
            '<div class="alert alert-info">Memproses absensi...</div>';

        fetch('/karyawan/absensi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('hasil-scan').innerHTML =
                    '<div class="alert alert-success">' + data.message + '</div>';
                setTimeout(() => location.reload(), 2000);
            } else {
                document.getElementById('hasil-scan').innerHTML =
                    '<div class="alert alert-danger">' + data.message + '</div>';
            }
        })
        .catch(() => {
            document.getElementById('hasil-scan').innerHTML =
                '<div class="alert alert-danger">Terjadi kesalahan, coba lagi.</div>';
        });
    }
</script>
@endsection