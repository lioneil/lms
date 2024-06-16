@extends('layouts::admin')

@section('head:title') @lang('Edit Library') @endsection
@section('page:title') @lang('Edit Library') @endsection

@section('page:content')
  @form(['action' => route('libraries.update', $resource->id)])
    @method('put')

    {{ $resource->name }}
    @field('text', ['name' => 'name', 'label' => 'Name', 'value' => $resource->name])
    {{ $resource->uri }}
    @field('text', ['name' => 'uri', 'label' => 'URI', 'value' => $resource->uri])
    {{ $resource->pathname }}
    @field('text', ['name' => 'pathname', 'label' => 'Pathname', 'value' => $resource->pathname])

    @submit('Update Library')
    button: Update Library
  @endform
@endsection
