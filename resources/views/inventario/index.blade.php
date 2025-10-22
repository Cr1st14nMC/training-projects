@extends('layouts.app')

@section('title', 'Control de Inventario')

@section('header', 'Control de Inventario')

@section('header-right')
    <a href="{{ route('inventario.movimientos') }}" class="btn btn-info">
        <i class="bi bi-list-ul"></i> Historial de Movimientos
    </a>
@endsection

@section('content')
<div id="app">
    <inventario-index></inventario-index>
</div>
@endsection