
@extends('layouts.app')

@section('title','Productos')
@section('header','Productos')

@section('content')
  <div id="app">
    <ProductoIndex 
        :initial-products='@json($productos)' 
        :initial-query="'{{ request('q', '') }}'">
    </ProductoIndex>
  </div>
@endsection