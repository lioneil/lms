@extends('layouts::admin')

@section('head:title') @lang('Edit Category') @endsection
@section('page:title') @lang('Edit Category') @endsection

@section('page:content')
  @form(['action' => route('categories.update', $resource->id)])
    @method('put')

    <div>Author: {{ $resource->user->displayname }}</div>
    {{ $resource->name }}
    @field('text', ['name' => 'name', 'label' => 'Name', 'value' => $resource->name])
    {{ $resource->code }}
    @field('text', ['name' => 'code', 'label' => 'Code', 'value' => $resource->code])
    {{ $resource->description }}
    @field('text', ['name' => 'description', 'label' => 'Description', 'value' => $resource->description])

    @submit('Update Category')
    button: Update Category
  @endform
@endsection

