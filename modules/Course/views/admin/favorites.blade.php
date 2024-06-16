@extends('layouts::admin')

@section('head:title') @lang('Favorite Courses') @endsection
@section('page:title') @icon('heart') @lang('Favorite Courses') @endsection

@section('page:content')
  @container(['attr' => 'grid-list-xl'])
    @layout(['attr' => 'row wrap'])
      @flex(['attr' => 'xs12'])

        @foreach ($resources as $resource)
          @card(['class' => 'mb-3', 'actions' => ['class' => 'grey lighten-3']])
            @slot('title') @a($resource->title, ['url' => route('courses.show', $resource->id)]) @endslot
            @slot('body'){{ $resource->overview }}@endslot
          @endcard
        @endforeach

      @endflex
    @endlayout
  @endcontainer
@stop
