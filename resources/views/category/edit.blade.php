@extends('layouts.app')

@section('title','Editar Categoría')
@section('header','Editar Categoría')

@section('content')
  <div id="app">
    <categoria-edit
      :category='@json($category)'
      update-url="{{ route('categories.update', $category->id) }}"
      cancel-url="{{ route('categories.index') }}"
    ></categoria-edit>
  </div>
@endsection