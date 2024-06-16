@extends('layouts::admin')

@section('head:title') @lang('Edit Page') @endsection
@section('page:title') @lang('Edit Page') @endsection

@section('page:content')
  @form(['action' => route('pages.update', $resource->id)])
    @method('put')

    <div>Author: {{ $resource->user->displayname }}</div>
    {{ $resource->title }}
    @field('text', ['name' => 'title', 'label' => 'Title', 'value' => $resource->title])
    {{ $resource->code }}
    @field('text', ['name' => 'code', 'label' => 'Code', 'value' => $resource->code])
    {{ $resource->feature }}
    @field('text', ['name' => 'feature', 'label' => 'Feature', 'value' => $resource->feature])

    @submit('Update Page')
    button: Update Page
  @endform
@endsection
