<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Karyawan') — York Food Store</title>

    <!-- Bootstrap 5 (sudah ada di project, sesuaikan path jika perlu) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-light">

    {{-- Navbar --}}
    <nav class="navbar navbar-dark bg-dark px-3">
        <span class="navbar-brand fw-bold">🏪 York Food Store — Panel Karyawan</span>
        @auth('employee')
        <form method="POST" action="{{ route('employee.logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm">Logout</button>
        </form>
        @endauth
    </nav>

    {{-- Konten Halaman --}}
    <div class="container mt-4">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>