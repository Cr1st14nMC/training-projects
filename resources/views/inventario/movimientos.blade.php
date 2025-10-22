@extends('layouts.app')

@section('title', 'Historial de Movimientos')

@section('header', 'Historial de Movimientos de Inventario')

@section('header-right')
    <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al Inventario
    </a>
@endsection

@section('content')
<div id="app">
    <inventario-movimientos></inventario-movimientos>
</div>
@endsection