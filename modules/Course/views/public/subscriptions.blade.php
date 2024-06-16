@extends('layouts::public')

@section('head:title') @lang('Subscribed Courses') @endsection
@section('page:title') @lang('Subscribed Courses') @endsection

@section('page:content')

  @foreach ($resources as $resource)
    @card
      @slot('body')
        <p>@a($resource->title, ['url' => route('courses.single', $resource->slug)])</p>
        <p>{{ $resource->subtitle }}</p>
        <p>{{ $resource->excerpt }}</p>
        <p>{{ $resource->author }}</p>
      @endslot
      @slot('footer')
        <small>{{ $resource->lessons->count() }} @lang('Lessons')</small>
      @endslot
    @endcard
    <hr>
  @endforeach

@stop
