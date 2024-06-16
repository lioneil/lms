@extends('layouts::admin')

@section('head:title') @lang('Create Course') @endsection
@section('page:title') @lang('Create Course') @endsection

@section('page:content')
  @form
    @lang('Title')
    @field('text', ['name' => 'title', 'label' => 'Title'])
    @lang('Subtitle')
    @field('text', ['name' => 'subtitle', 'label' => 'Subtitle'])
    @lang('Code')
    @field('text', ['name' => 'code', 'label' => 'Code'])
    @field('text', ['name' => 'description', 'label' => 'Description'])
    @lang('Overview')

    @field('text', ['name' => 'email', 'label' => 'Email'])

    @lang('Lessons')

    {{-- @submit('Save Course') --}}
    button: @lang('Save Course')
  @endform
@endsection
