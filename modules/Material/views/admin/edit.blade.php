@extends('layouts::admin')

@section('head:title') @lang('Edit Material') @endsection
@section('page:title') @lang('Edit Material') @endsection

@section('page:content')
  @form(['action' => route('materials.update', $resource->id)])
    @method('put')

    {{ $resource->title }}
    @field('text', ['name' => 'title', 'label' => 'Title', 'value' => $resource->title])
    {{ $resource->uri }}
    @field('text', ['name' => 'uri', 'label' => 'URI', 'value' => $resource->uri])
    {{ $resource->pathname }}
    @field('text', ['name' => 'pathname', 'label' => 'Pathname', 'value' => $resource->pathname])

    @submit('Update Material')
    button: Update Material
  @endform
@endsection
