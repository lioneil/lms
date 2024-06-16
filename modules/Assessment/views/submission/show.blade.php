@extends('layouts::admin')

@section('head:title') @lang($resource->results) @endsection
@section('page:title') @lang($resource->results) @endsection

@section('page:buttons')
  @can('submissions.edit', $resource)
    @lang('Edit')
  @endcan
  @can('submissions.destroy', $resource)
    Move to Trash
    <form action="{{ route('submissions.destroy', $resource->getKey()) }}" method="post">
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
        <h1 class="page-title">{{ $resource->results }}</h1>
        <h6>{{ $resource->remarks }}</h6>
      @endcolumn
    @endrow
  @endcontainer
@endsection
