@extends('layouts::admin')

@section('head:title') @lang('Create Page') @endsection
@section('page:title') @lang('Create Page') @endsection

@section('page:content')
  @form
    @lang('Title')
    @field('text', ['name' => 'title', 'label' => 'Title'])
    @lang('Code')
    @field('text', ['name' => 'code', 'label' => 'Code'])
    @lang('Feature')
    @field('text', ['name' => 'feature', 'label' => 'Feature'])

    button: @lang('Save Page')
  @endform
@endsection
