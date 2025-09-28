@extends('layouts.app')

@section('title','Editar Categoría')
@section('header','Editar Categoría')

@section('content')
  <div id="app">
    <CategoriaEdit
      :category='@json($category)'
      update-url="{{ route('categories.update', $category) }}"
      cancel-url="{{ route('categories.index') }}"
    ></CategoriaEdit>
  </div>
    <script src="{{ mix('js/app.js') }}"></script>

@endsection
