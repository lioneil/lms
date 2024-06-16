@extends('layouts::admin')

@section('head:title') @lang($resource->name) @endsection
@section('page:title') @lang($resource->name) @endsection

@section('page:buttons')
  @can('libraries.edit', $resource)
    @lang('Edit')
  @endcan
  @can('libraries.destroy', $resource)
    Move to Trash
    <form action="{{ route('libraries.destroy', $resource->getKey()) }}" method="post">
      @csrf
      @method('delete')
      @submit('Move to Trash')
    </form>
  @endcan
@endsection

@section('page:content')
  @container
    @row
      @column
        <h1 class="page-title">{{ $resource->name }}</h1>
        <h6>{{ $resource->uri }}</h6>
        <h5>{{ $resource->pathname }}</h5>
      @endcolumn
    @endrow
  @endcontainer
@endsection
