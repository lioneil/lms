@extends('layouts::admin')

@section('page:title')@lang('Edit Role')@endsection

@section('page:buttons')
    @foreach ($service->permissions() as $resource)
      <p>{{ field('permissions[]')->id('cb-'.$resource->id)->type('checkbox')->label($resource->name)->value($resource->id) }}</p>
    @endforeach
@stop
