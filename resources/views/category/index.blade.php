@extends('layouts.app')

@section('title', 'Categorías')
@section('header', 'Categorías')

@section('content')
  <div id="app">
    <categoria-index
      fetch-url="{{ route('categories.index') }}" 
      create-url="{{ route('categories.create') }}"
      edit-base-url="{{ url('categories') }}"
      destroy-base-url="{{ url('categories') }}"
    ></categoria-index>
  </div>
@endsection