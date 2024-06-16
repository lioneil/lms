@extends('layouts::admin')

@section('head:title') @lang('Edit Assignment') @endsection
@section('page:title') @lang('Edit Assignment') @endsection

@section('page:content')
  @form(['action' => route('assignments.update', $resource->id)])
    @method('put')

    <div>Author: {{ $resource->user->displayname }}</div>
    {{ $resource->title }}
    @field('text', ['name' => 'title', 'label' => 'Title', 'value' => $resource->title])
    {{ $resource->uri }}
    @field('text', ['name' => 'uri', 'label' => 'URI', 'value' => $resource->uri])
    {{ $resource->pathname }}
    @field('text', ['name' => 'pathname', 'label' => 'Pathname', 'value' => $resource->pathname])

    @submit('Update Assignment')
    button: Update Assignment
  @endform
@endsection
