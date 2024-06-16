@extends('layouts::admin')

@section('head:title') @lang('Create Thread') @endsection
@section('page:title') @lang('Create Thread') @endsection

@section('page:content')
  @form
    @lang('Title')
    @field('text', ['name' => 'title', 'label' => 'Title'])
    @lang('Slug')
    @field('text', ['name' => 'slug', 'label' => 'Slug'])
    @lang('Body')
    @field('text', ['name' => 'body', 'label' => 'Body'])

    button: @lang('Save Thread')
  @endform
@endsection
