@extends('layouts::admin')

@section('head:title') @lang('Create Library') @endsection
@section('page:title') @lang('Create Library') @endsection

@section('page:content')
  @form
    @lang('Name')
    @field('text', ['name' => 'name', 'label' => 'Name'])
    @lang('URI')
    @field('text', ['name' => 'uri', 'label' => 'URI'])
    @lang('Pathname')
    @field('text', ['name' => 'pathname', 'label' => 'Pathname'])

    button: @lang('Save Library')
  @endform
@endsection
