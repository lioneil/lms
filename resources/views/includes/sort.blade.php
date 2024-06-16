@if ($key === request()->get('sort'))
  @switch(request()->get('order'))
    @case('asc')
      <a href="{{ __url(['sort' => $key, 'order' => 'desc']) }}">@lang($label)&nbsp;<i class="mdi mdi-sort-ascending"></i></a>
      @break

    @case('desc')
      <a href="{{ __url(['sort' => null, 'order' => null]) }}">@lang($label)&nbsp;<i class="mdi mdi-sort-descending"></i></a>
      @break
  @endswitch
@else
  <a href="{{ __url(['sort' => $key, 'order' => 'asc']) }}">@lang($label)</a>
@endif
