@extends('layouts::admin')

@section('head:title') @lang('Archived Courses') @endsection
@section('page:title') @lang('Archived Courses') @endsection

@section('page:back')
  @a('Back to Courses', ['url' => route('courses.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $i => $resource)
          <hr>
          <h1>{{ $i + 1 }}</h1>
          <h1>{{ $resource->getKey() }}</h1>
          <p>{{ $resource->title }}</p>
          <p>{{ $resource->subtitle }}</p>
          <p>{{ $resource->code }}</p>
          <p>{{ $resource->status }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <form action="{{ route('courses.restore', $resource->getKey()) }}" method="post">
            @csrf
            @method('patch')
            <button type="submit">Restore</button>
          </form>
          <p>@a('Remove Permanently', ['url' => route('courses.delete', $resource->getKey())])</p>
          <form action="{{ route('courses.delete', $resource->getKey()) }}" method="post">
            @csrf
            @method('delete')
            <button type="submit">Remove Permanently</button>
          </form>
          <hr>
        @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
