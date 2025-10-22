@extends('layouts.app')

@section('title', 'Nueva Compra')

@section('header', 'Registrar Nueva Compra')

@section('header-right')
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')
<div id="app">
    <compra-create></compra-create>
</div>
@endsection