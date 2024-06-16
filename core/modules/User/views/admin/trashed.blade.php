@extends('layouts::admin')

@section('page:content')
  {{-- form --}}
  @searchform

  @foreach ($resources as $resource)
    <p>{{ $resource->displayname }}</p>
  @endforeach
@endsection
