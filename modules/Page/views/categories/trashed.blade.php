@extends('layouts::admin')

@section('head:title') @lang('Archived Categories') @endsection
@section('page:title') @lang('Archived Categories') @endsection

@section('page:back')
  @a('Back to Categories', ['url' => route('categories.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $i => $resource)
          <hr>
          <h1>{{ $i + 1 }}</h1>
          <p>{{ $resource->name }}</p>
          <p>{{ $resource->code }}</p>
          <p>{{ $resource->description }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <form action="{{ route('categories.restore', $resource->getKey()) }}" method="post">
            @csrf
            @method('patch')
            <button type="submit">Restore</button>
          </form>
          <p>@a('Remove Permanently', ['url' => route('categories.delete', $resource->getKey())])</p>
          </hr>
          @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
