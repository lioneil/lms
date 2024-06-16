@extends('layouts::admin')

@section('head:title') @lang($resource->name) @endsection
@section('page:title') @lang($resource->name) @endsection

@section('page:buttons')
  @can('categories.edit', $resource)
    @lang('Edit')
  @endcan
  @can('categories.destroy', $resource)
    Move to Trash
    <form action="{{ route('categories.destroy', $resource->getKey()) }}" method="post">
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
        <h2 class="page-title">{{ $resource->code }}</h1>
        <h3 class="page-title">{{ $resource->description }}</h1>
      @endcolumn
    @endrow
  @endcontainer
@endsection
