@extends('layouts::master')

@section('master')
  @section('app:sidebar')
    @sidebar
  @show

  @section('workspace')
    @workspace
      @section('app:utilitybar')
        @utilitybar
      @show
      @section('app:breadcrumbs')
        @breadcrumbs
      @show

      <main id="main">
        @section('main')
          @section('page')
            @section('page:header')
              @container
                @layout
                  @flex
                    @yield('page:back')
                    <h1>@section('page:title') @lang(@$application->page->title) @show</h1>
                    @yield('page:buttons')
                    @yield('page:button')
                  @endflex
                @endlayout
              @endcontainer
            @show

            @yield('page:content')

            @stack('page:footer')
          @show
        @show
      </main>

      @section('app:endnote')
        @endnote
      @show
    @endworkspace
  @show
@stop

@section('footer')
  @snackbar
@endsection
