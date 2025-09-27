<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Productos - Vue + Mix</title>

  <!-- tu CSS compilado -->
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">

  <!-- opcional: bootstrap CDN si no lo compilas -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
  <div class="container py-4" id="app">
    <h1 class="mb-4">Productos</h1>

    <!-- pasa los productos iniciales desde blade a Vue -->
    <Index :initial-products='@json($productos)'></Index>
  </div>

  <!-- JS compilado por Mix (contiene Vue) -->
  <script src="{{ mix('js/app.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
