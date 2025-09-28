@extends('layouts.app')

@section('title','Categorías')
@section('header','Categorías')

@section('content')
  <div id="app">
    <CategoriaIndex
      fetch-url="{{ route('categories.index') }}" 
      create-url="{{ route('categories.create') }}"
      edit-base-url="{{ url('categories') }}"
      destroy-base-url="{{ url('categories') }}"
    ></CategoriaIndex>
  </div>
    <!-- JS compilado por Mix (contiene Vue) -->
  <script src="{{ mix('js/app.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
