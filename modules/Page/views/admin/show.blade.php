@extends('layouts::admin')

@section('head:title') @lang($resource->title) @endsection
@section('page:title') @lang($resource->title) @endsection

@section('page:buttons')
  @can('pages.edit', $resource)
    @lang('Edit')
  @endcan
  @can('pages.destroy', $resource)
    Move to Trash
    <form action="{{ route('pages.destroy', $resource->getKey()) }}" method="post">
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
        <h6>{{ $resource->code }}</h6>
        <h5>{{ $resource->feature }}</h5>
      @endcolumn
    @endrow
  @endcontainer
@endsection