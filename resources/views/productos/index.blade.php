
@extends('layouts.app')

@section('title','Productos')
@section('header','Productos')

@section('content')
  <div id="app">
    <producto-index 
        :initial-products='@json($productos)' 
        :initial-query="'{{ request('q', '') }}'">
    </producto-index>

  </div>
@endsection