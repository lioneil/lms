@include('theme::partials.head')

@section('app')
  <div id="app">
    @app
      @section('master')
        @stack('before:main')

        @section('main')
          <main id="main">
            @stack('before:page')

            @yield('page')

            @stack('after:page')
          </main>
        @show

        @stack('after:main')
      @show
    @endapp
  </div>
@show

@include('theme::debug.debugbar')
@include('theme::partials.foot')
