@extends('layouts::admin')

@section('head:title') @lang('Preview Thread') @endsection
@section('page:title') @lang('Preview Thread') @endsection

@section('page:buttons')
  @can('threads.edit', $resource)
    @lang('Continue Editing')
  @endcan
  @can('threads.destroy', $resource)
    Move to Trash
    <form action="{{ route('threads.destroy', $resource->getKey()) }}" method="post">
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
        <h6>{{ $resource->slug }}</h6>
        <h5>{{ $resource->body }}</h5>
      @endcolumn
    @endrow
  @endcontainer
@endsection
