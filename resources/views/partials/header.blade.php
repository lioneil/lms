<div data-sticky="#page-header"></div>
<header id="page-header" data-sticky-class="bg-workspace sticky" class="d-block page-header">
  @yield('page:back')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="page-title">@section('page:title'){{ '@page()->title()' }}@show</h1>
    <div>@yield('page:button')</div>
  </div>
  @yield('page:subtitle')
  @yield('page:buttons')
</header>
