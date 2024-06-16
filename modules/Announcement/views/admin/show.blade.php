@extends('layouts::admin')

@section('head:title') @lang($resource->title) @endsection
@section('page:title') @lang($resource->title) @endsection

@section('page:buttons')
  @can('announcements.edit', $resource)
    @lang('Edit')
  @endcan
  @can('announcements.destroy', $resource)
    Move to Trash
    <form action="{{ route('announcements.destroy', $resource->getKey()) }}" method="post">
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
      @endcolumn
    @endrow
  @endcontainer
@endsection
