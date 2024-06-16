@extends('layouts::settings', ['setting' => 'module:widget'])

@section('head:title', __($resource['fullname']))
@section('page:title', __($resource['fullname']))

@section('page:content')

  @widget($resource['alias'])

@stop
