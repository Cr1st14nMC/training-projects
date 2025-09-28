<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Mi App')</title>

  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body { min-height: 100vh; }
    .sidebar { min-width: 220px; max-width: 250px; }
    .content { flex: 1 1 auto; padding: 20px; }
  </style>
</head>
<body>
  <div class="d-flex">
<!-- Sidebar -->
<nav class="sidebar d-flex flex-column p-3 shadow" style="min-height: 100vh;">
  <h4 class="text-center mb-4">Panel de control</h4>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item mb-2">
      <a class="nav-link d-flex align-items-center gap-2" href="{{ route('productos.index') }}">
        <i class="bi bi-bag-fill"></i> 
        <span>Productos</span>
      </a>
    </li>
    <li class="nav-item mb-2">
      <a class="nav-link d-flex align-items-center gap-2" href="{{ route('categories.index') }}">
        <i class="bi bi-tags-fill"></i> 
        <span>Categorías</span>
      </a>
    </li>
  </ul>
</nav>

<style>

</style>


    <!-- Main content -->
    <div class="content">
      <header class="d-flex justify-content-between align-items-center mb-4">
        <h2>@yield('header', 'Dashboard')</h2>
        <div>@yield('header-right')</div>
      </header>

      <!-- Mensajes de sesión -->
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <!-- Contenido principal -->
      @yield('content')
    </div>
  </div>

<script src="{{ mix('js/app.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
