@extends('layouts.app')

@section('title','Crear Categoría')
@section('header','Crear Categoría')

@section('content')
  <div id="app">
    <categoria-create
      store-url="{{ route('categories.store') }}"
      cancel-url="{{ route('categories.index') }}"
    ></categoria-create>
  </div>
@endsection

