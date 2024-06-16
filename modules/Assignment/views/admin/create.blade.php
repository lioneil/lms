@extends('layouts::admin')

@section('head:title') @lang('Create Assignment') @endsection
@section('page:title') @lang('Create Assignment') @endsection

@section('page:content')
  @form
    @lang('Title')
    @field('text', ['name' => 'title', 'label' => 'Title'])
    @lang('URI')
    @field('text', ['name' => 'uri', 'label' => 'URI'])
    @lang('Pathname')
    @field('text', ['name' => 'pathname', 'label' => 'Pathname'])

    button: @lang('Save Assignment')
  @endform
@endsection
