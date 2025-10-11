@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('header', 'Registrar Nueva Venta')

@section('header-right')
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')
<div id="app">
    <venta-create></venta-create>
</div>
@endsection