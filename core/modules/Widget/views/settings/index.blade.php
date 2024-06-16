@extends('layouts::settings')

@section('head:title', __('Widgets'))
@section('page:title', __('Widgets'))

@section('page:content')

  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-8 mx-auto">
        @foreach ($resources as $resource)
          @card
            @slot('title')
              <a href="{{ route('widgets.show', $resource['alias']) }}"><strong>@lang($resource['fullname'])</strong></a>
              <p>name</p>
              <p>description</p>
            @endslot
            @slot('body')
              <p class="text-muted">@icon('mdi mdi-code-tags'){{ $resource['alias'] }}</p>
              <p>@lang($resource['description'])</p>
            @endslot
          @endcard
        @endforeach
      </div>
    </div>
  </div>

@stop
