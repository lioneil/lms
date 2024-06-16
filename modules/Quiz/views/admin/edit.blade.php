@extends('layouts::admin')

@section('head:title') @lang('Edit Quiz') @endsection
@section('page:title') @lang('Edit Quiz') @endsection

@section('page:content')
  @form(['action' => route('quizzes.update', $resource->id)])
    @method('put')

    {{ $resource->title }}
    @field('text', ['name' => 'title', 'label' => 'Title', 'value' => $resource->title])
    {{ $resource->url }}
    @field('text', ['name' => 'url', 'label' => 'URL', 'value' => $resource->url])
    {{ $resource->description }}
    @field('text', ['name' => 'description', 'label' => 'Description', 'value' => $resource->description])

    @submit('Update Quiz')
    button: Update Quiz
  @endform
@endsection
