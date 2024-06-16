@extends('layouts::admin')

@section('head:title') @lang('Create Quiz') @endsection
@section('page:title') @lang('Create Quiz') @endsection

@section('page:content')
  @form
    @lang('Title')
    @field('text', ['name' => 'title', 'label' => 'Title'])
    @lang('Description')
    @field('text', ['name' => 'description', 'label' => 'Description'])

    button: @lang('Save Quiz')
  @endform
@endsection
