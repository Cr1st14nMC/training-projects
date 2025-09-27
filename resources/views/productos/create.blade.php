<!DOCTYPE html>
<html>

<head>
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div id="app">
        <Create store-url="{{ route('productos.store') }}" cancel-url="{{ route('productos.index') }}">
        </Create>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>