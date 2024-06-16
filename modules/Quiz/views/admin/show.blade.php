@extends('layouts::admin')

@section('head:title') @lang($resource->title) @endsection
@section('page:title') @lang($resource->title) @endsection

@section('page:buttons')
  @can('quizzes.edit', $resource)
    @lang('Edit')
  @endcan
  @can('quizzes.destroy', $resource)
    Move to Trash
    <form action="{{ route('quizzes.destroy', $resource->getKey()) }}" method="post">
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
        <h6>{{ $resource->description }}</h6>
      @endcolumn
    @endrow
  @endcontainer
@endsection
