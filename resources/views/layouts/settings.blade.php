@extends('layouts::admin')

@section('main')
  @section('page:header')
    @header
      @slot('title')
        @yield('page:title')
      @endslot
      @slot('button')
        @yield('page:button')
      @endslot
      @yield('page:subtitle')
      @yield('page:buttons')
    @endheader
  @show
  @section('page:sidebar')
    @if (sidebar()->get('module:setting')->children($setting ?? null))
      @foreach (sidebar()->get('module:setting')->children($setting ?? null) ?: [] as $menu)
        <div class="nav-item mr-3 {{ $menu->active() ? 'active' : null }}">
          <a href="{{ $menu->url() }}" class="nav-link  {{ $menu->active() ? 'active' : null }}">
            @lang($menu->text())
          </a>
        </div>
      @endforeach
    @endif
    @if(! empty(sidebar()->get('module:setting')->children($setting ?? null)))
      @if (sidebar()->get('module:setting')->children($setting ?? null)->isEmpty())
        @foreach (sidebar()->get('module:setting')->children($setting ?? null) ?: [] as $menu)
          <div class="nav-item mr-3 {{ $menu->active() ? 'active' : null }}">
            <a href="{{ $menu->url() }}" class="nav-link  {{ $menu->active() ? 'active' : null }}">
              @lang($menu->text())
            </a>
          </div>
        @endforeach
      @endif
    @endempty
  @show
  @section('page:content')
  @form(['action' => route('settings.store')])
    @yield('form:content')
    @submit('Save Options')
  @endform
  @show
@stop
