@extends('layouts::admin')

@section('head:title') @lang('Edit Course') @endsection
@section('page:title') @lang('Edit Course') @endsection

@section('page:content')
  @form(['action' => route('courses.update', $resource->id)])
    @method('put')

    <div>Author: {{ $resource->user->displayname }}</div>
    {{ $resource->title }}
    @field('text', ['name' => 'title', 'label' => 'Title', 'value' => $resource->title])
    {{ $resource->subtitle }}
    @field('text', ['name' => 'subtitle', 'label' => 'Subtitle', 'value' => $resource->subtitle])
    {{ $resource->code }}
    @field('text', ['name' => 'code', 'label' => 'Code', 'value' => $resource->code])
    {{ $resource->description }}
    {{-- @field('editor', ['name' => 'description', 'label' => 'Overview', 'value' => $resource->description]) --}}

    {{ $resource->logo }}
    {{-- @field('upload', ['name' => 'image', 'label' => 'Illustration', 'value' => $resource->logo]) --}}

    @lang('Lessons')

    Author
    {{ $resource->user->getKey() }}

    Category
    {{ $resource->category->getKey() }}

    Tags
    @foreach ($resource->tags as $tag)
      <p>{{ $tag->getKey() }}</p>
    @endforeach
    {{-- Lessons form goes here --}}

    @submit('Update Course')
    button: Update Course
  @endform
@endsection
