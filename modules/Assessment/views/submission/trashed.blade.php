@extends('layouts::admin')

@section('head:title') @lang('Archived Submissions') @endsection
@section('page:title') @lang('Archived Submissions') @endsection

@section('page:back')
  @a('Back to Submissions', ['url' => route('submissions.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $i => $resource)
          <hr>
          <h1>{{ $i + 1 }}</h1>
          <p>{{ $resource->results }}</p>
          <p>{{ $resource->remarks }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <form action="{{ route('submissions.restore', $resource->getKey()) }}" method="post">
            @csrf
            @method('patch')
            <button type="submit">Restore</button>
          </form>
          <p>@a('Remove Permanently', ['url' => route('submissions.delete', $resource->getKey())])</p>
          </hr>
          @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
