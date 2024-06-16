@extends('layouts::admin')

@section('head:title') @lang('Edit Thread') @endsection
@section('page:title') @lang('Edit Thread') @endsection

@section('page:content')
  @form(['action' => route('threads.update', $resource->id)])
    @method('put')

    <div>Author: {{ $resource->user->displayname }}</div>
    {{ $resource->title }}
    @field('text', ['name' => 'title', 'label' => 'Title', 'value' => $resource->title])
    {{ $resource->slug }}
    @field('text', ['name' => 'slug', 'label' => 'Slug', 'value' => $resource->slug])
    {{ $resource->body }}
    @field('text', ['name' => 'body', 'label' => 'Body', 'value' => $resource->body])

    @submit('Update Thread')
    button: Update Thread
  @endform
@endsection
