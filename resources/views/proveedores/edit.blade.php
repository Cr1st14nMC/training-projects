@extends('layouts.app')
@section('title', 'Editar Proveedor')
@section('header', 'Editar Proveedor')

@section('header-right')
    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')
<div class="container py-4">
    <div id="app">
        <proveedor-edit
            :proveedor='@json($proveedor)'
            {{-- CORRIGE ESTA LÍNEA --}}
            update-url="{{ route('proveedores.update', $proveedor) }}"
            cancel-url="{{ route('proveedores.index') }}"
        ></proveedor-edit>
    </div>
</div>
@endsection
