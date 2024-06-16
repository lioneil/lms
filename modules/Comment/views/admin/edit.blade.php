@extends('layouts::admin')

@section('head:title') @lang('Edit Comment') @endsection
@section('page:title') @lang('Edit Comment') @endsection

@section('page:content')
  @form(['action' => route('comments.update', $resource->id)])
    @method('put')

    <div>Author: {{ $resource->user->displayname }}</div>
    {{ $resource->body }}
    @field('text', ['name' => 'body', 'label' => 'Body', 'value' => $resource->body])

    @submit('Update Comment')
    button: Update Comment
  @endform
@endsection

