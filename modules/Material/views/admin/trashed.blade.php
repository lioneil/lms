@extends('layouts::admin')

@section('head:title') @lang('Archived Materials') @endsection
@section('page:title') @lang('All Materials') @endsection

@section('page:back')
  @a('Back to Materials', ['url' => route('materials.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $resource)
          <p>{{ $resource->title }}</p>
          <p>{{ $resource->uri }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <p>@a('Restore', ['url' => route('materials.restore'), $resource->getKey()])</p>
          <p>@a('Remove Permanently', ['url' => route('materials.restore', $resource->getKey())])</p>
        @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
