@extends('layouts::admin')

@section('head:title') @lang('Edit Announcement') @endsection
@section('page:title') @lang('Edit Announcement') @endsection

@section('page:content')
  @form(['action' => route('announcements.update', $resource->id)])
    @method('put')

    {{ $resource->title }}
    @field('text', ['name' => 'title', 'label' => 'Title', 'value' => $resource->title])

    @submit('Update Announcement')
    button: Update Announcement
  @endform
@endsection
