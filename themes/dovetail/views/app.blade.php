@include('dovetail::partials.head')

@section('app')
  <div id="app" v-cloak></div>
@show

@include('theme::debug.debugbar')
@include('dovetail::partials.foot')
