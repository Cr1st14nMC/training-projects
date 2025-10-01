@extends('layouts.app')

@section('title', 'Agregar Producto')
@section('header', 'Agregar Producto')

@section('content')
  <div id="app">
    <producto-create
      :categories='@json($categories)'
      store-url="{{ route('productos.store') }}" 
      cancel-url="{{ route('productos.index') }}"
      ></producto-create>
  </div>
@endsection