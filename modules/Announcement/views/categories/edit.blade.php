@extends('layouts::admin')

@section('head:title') @lang('Edit Announcement-Category') @endsection
@section('page:title') @lang('Edit Announcement-Category') @endsection

@section('page:content')
  @form(['action' => route('categories.update', $resource->id)])
    @method('put')

    {{ $resource->name }}
    @field('text', ['name' => 'name', 'label' => 'Name', 'value' => $resource->name])

    @submit('Update Announcement-Category')
    button: Update Announcement-Category
  @endform
@endsection
