@extends('layouts.app')

@section('title', 'Compras')

@section('header', 'Gestión de Compras')

@section('header-right')
    <a href="{{ route('compras.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Compra
    </a>
@endsection

@section('content')
<div id="app">
    <compra-index></compra-index>
</div>
@endsection