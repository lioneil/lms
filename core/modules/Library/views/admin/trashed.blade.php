@extends('layouts::admin')

@section('head:title') @lang('Archived Libraries') @endsection
@section('page:title') @lang('All Libraries') @endsection

@section('page:back')
  @a('Back to Libraries', ['url' => route('libraries.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $resource)
          <p>{{ $resource->name }}</p>
          <p>{{ $resource->url }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <p>@a('Restore', ['url' => route('libraries.restore'), $resource->getKey()])</p>
          <p>@a('Remove Permanently', ['url' => route('libraries.restore', $resource->getKey())])</p>
        @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
