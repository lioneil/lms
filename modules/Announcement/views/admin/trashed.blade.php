@extends('layouts::admin')

@section('head:title') @lang('Archived Announcements') @endsection
@section('page:title') @lang('All Announcements') @endsection

@section('page:back')
  @a('Back to Announcements', ['url' => route('announcements.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $resource)
          <p>{{ $resource->title }}</p>
          <p>@a('Restore', ['url' => route('announcements.restore'), $resource->getKey()])</p>
          <p>@a('Remove Permanently', ['url' => route('announcements.restore', $resource->getKey())])</p>
        @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
