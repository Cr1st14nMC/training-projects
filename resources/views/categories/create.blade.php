@extends('layouts.app')

@section('title','Crear Categoría')
@section('header','Crear Categoría')

@section('content')
  <div id="app">
    <!-- Pasamos las URLs necesarias -->
    <CategoriaCreate
      store-url="{{ route('categories.store') }}"
      cancel-url="{{ route('categories.index') }}"
    ></CategoriaCreate>
  </div>
    <script src="{{ mix('js/app.js') }}"></script>

@endsection
