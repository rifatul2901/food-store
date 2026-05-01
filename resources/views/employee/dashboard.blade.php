@extends('employee.layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold">
            Selamat datang, {{ session('employee')['name'] }}
        </h5>
        <form method="POST" action="{{ route('employee.logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-dark btn-sm">Logout</button>
        </form>
    </div>

    <div class="alert alert-info">
        Dashboard absensi akan tersedia di Minggu 3.
    </div>
</div>
@endsection
