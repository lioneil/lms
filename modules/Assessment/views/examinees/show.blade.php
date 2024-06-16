@extends('layouts::admin')

@section('head:title') @lang($resource->title) @endsection
@section('page:title') @lang($resource->title) @endsection

@section('page:content')
  @container
    @row
      @column
        <h1 class="page-title">{{ $resource->title }}</h1>
        <h6>{{ $resource->code }}</h6>
        <h6>{{ $resource->type }}</h6>
      @endcolumn
    @endrow
  @endcontainer
@endsection
