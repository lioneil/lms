@extends('layouts::admin')

@section('head:title') @lang('Archived Quizzes') @endsection
@section('page:title') @lang('All Quizzes') @endsection

@section('page:back')
  @a('Back to Quizzes', ['url' => route('quizzes.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $resource)
          <p>{{ $resource->title }}</p>
          <p>{{ $resource->description }}</p>

          <p>@a('Restore', ['url' => route('quizzes.restore'), $resource->getKey()])</p>
          <p>@a('Remove Permanently', ['url' => route('quizzes.restore', $resource->getKey())])</p>
        @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
