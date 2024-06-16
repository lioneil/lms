@extends('layouts::admin')

@section('head:title') @lang('Archived Pages') @endsection
@section('page:title') @lang('Archived Pages') @endsection

@section('page:back')
  @a('Back to Pages', ['url' => route('pages.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $i => $resource)
          <hr>
          <h1>{{ $i + 1 }}</h1>
          <p>{{ $resource->title }}</p>
          <p>{{ $resource->code }}</p>
          <p>{{ $resource->feature }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <form action="{{ route('pages.restore', $resource->getKey()) }}" method="post">
            @csrf
            @method('patch')
            <button type="submit">Restore</button>
          </form>
          <p>@a('Remove Permanently', ['url' => route('pages.delete', $resource->getKey())])</p>
          </hr>
          @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
