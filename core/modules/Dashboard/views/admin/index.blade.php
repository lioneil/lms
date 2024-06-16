@extends('layouts::admin')

@section('page:content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg">

        {{-- @foreach (widgets()->group('dash:topshelf')->all() as $widget)
          @can('widgets.show', $widget['alias'])
            @widget($widget['alias'])
          @endcan
        @endforeach --}}

        <div class="row">
          {{-- <div class="col-lg">@widget('xx:pors')</div> --}}
          <div class="col-lg">@widget('app:details')</div>
          <div class="col-lg">@widget('user:count')</div>
        </div>

      </div>
    </div>
  </div>
@endsection
