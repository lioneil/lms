@extends('layouts::admin')

@section('head:title') @lang($resource->title) @endsection
@section('page:title') @lang($resource->title) @endsection

@section('page:buttons')
  @can('materials.edit', $resource)
    @lang('Edit')
  @endcan
  @can('materials.destroy', $resource)
    Move to Trash
    <form action="{{ route('materials.destroy', $resource->getKey()) }}" method="post">
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
        <h1 class="page-title">{{ $resource->title }}</h1>
        <h6>{{ $resource->uri }}</h6>
        <h5>{{ $resource->pathname }}</h5>
      @endcolumn
    @endrow
  @endcontainer
@endsection
