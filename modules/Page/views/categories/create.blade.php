@extends('layouts::admin')

@section('head:title') @lang('Create Category') @endsection
@section('page:title') @lang('Create Category') @endsection

@section('page:content')
  @form
    @lang('Name')
    @field('text', ['name' => 'name', 'label' => 'Name'])
    @lang('Code')
    @field('text', ['name' => 'code', 'label' => 'Code'])
    @lang('Description')
    @field('text', ['name' => 'description', 'label' => 'Description'])

    button: @lang('Save Category')
  @endform
@endsection
