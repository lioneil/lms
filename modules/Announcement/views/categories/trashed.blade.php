@extends('layouts::admin')

@section('head:title') @lang('Archived Announcements') @endsection
@section('page:title') @lang('All Announcements') @endsection

@section('page:back')
  @a('Back to Announcement-Categories', ['url' => route('categories.index')])
@endsection

@section('page:content')
  @container
    @row
      @column

        @foreach ($resources as $resource)
          <p>{{ $resource->name }}</p>
          <p>@a('Restore', ['url' => route('categories.restore'), $resource->getKey()])</p>
          <p>@a('Remove Permanently', ['url' => route('categories.restore', $resource->getKey())])</p>
        @endforeach

      @endcolumn
    @endrow
  @endcontainer
@endsection
