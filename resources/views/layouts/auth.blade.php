@extends('layouts::master')

@section('auth')
  @section('main')
    <main id="main">
      <div id="app">
        @app
          @yield('page:header')
          @yield('page:content')
          @yield('page:footer')
        @endapp
      </div>
    </main>
  @endsection
@show
