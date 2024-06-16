@extends('layouts::admin')

@section('head:title') @lang('Archived Threads') @endsection
@section('page:title') @lang('Archived Threads') @endsection

@section('page:back')
  @a('Back to Threads', ['url' => route('threads.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $i => $resource)
          <hr>
          <h1>{{ $i + 1 }}</h1>
          <p>{{ $resource->title }}</p>
          <p>{{ $resource->slug }}</p>
          <p>{{ $resource->body }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <form action="{{ route('threads.restore', $resource->getKey()) }}" method="post">
            @csrf
            @method('patch')
            <button type="submit">Restore</button>
          </form>
          <p>@a('Remove Permanently', ['url' => route('threads.delete', $resource->getKey())])</p>
          </hr>
          @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
