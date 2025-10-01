@extends('layouts.app')

@section('title', 'Editar Producto')
@section('header', 'Editar Producto')

@section('content')
    <div id="app">
        <producto-edit 
            :producto='@json($producto)'
            :categories='@json($categories)'
            update-url="{{ route('productos.update', $producto->id) }}"
            cancel-url="{{ route('productos.index') }}"
        ></producto-edit>
    </div>
@endsection