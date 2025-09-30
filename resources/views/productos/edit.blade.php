@extends('layouts.app')

@section('title','Productos')
@section('header','Productos')

<!DOCTYPE html>
<html>

<head>
    <title>Editar Producto</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div id="app">
        <ProductoEdit :producto='@json($producto)'></ProductoEdit>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>