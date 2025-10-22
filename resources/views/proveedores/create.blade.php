@extends('layouts.app')
@section('title', 'Nuevo Proveedor')
@section('header', 'Registrar Nuevo Proveedor')

@section('header-right')
    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')
<div id="app">
    <proveedor-create></proveedor-create>
</div>
@endsection