@extends('layouts::admin')

@section('head:title') @lang('Create Announcement') @endsection
@section('page:title') @lang('Create Announcement') @endsection

@section('page:content')
  @form
    @lang('Title')
    @field('text', ['name' => 'title', 'label' => 'Title'])

    button: @lang('Save Announcement')
  @endform
@endsection
