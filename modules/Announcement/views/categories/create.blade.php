@extends('layouts::admin')

@section('head:title') @lang('Create Announcement-Category') @endsection
@section('page:title') @lang('Create Announcement-Category') @endsection

@section('page:content')
  @form
    @lang('Name')
    @field('text', ['name' => 'name', 'label' => 'Name'])

    button: @lang('Save Announcement-Category')
  @endform
@endsection
