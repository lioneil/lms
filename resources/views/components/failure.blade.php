@extends('layouts.blank')

@section('head:title') {{ $head }} @endsection

@section('page:content')
  @container(['attr' => 'fill-height'])
    @layout(['attr' => 'justify-center align-center'])
      @flex(['attr' => 'md6 xs12'])
        @card(['attr' => 'flat', 'class' => 'transparent text-xs-center'])
          @slot('body')
            <div class="py-4"><h4 class="muted--text mb-2">{{ $title }}</h4></div>
            <h1 class="mb-2">{{ $subtitle }}</h1>
            <p class="muted--text">{{ $message }}</p>
            @isset($button)
              {{ $button }}
            @endisset

            {{ $slot }}

            @isset($superadminsonly)
              @include('theme::errors.superadminsonly')
            @endisset
          @endslot
        @endcard
      @endflex

      @flex(['attr' => 'md5 xs12  offset-md1'])
        @illustration('error')
      @endflex
    @endlayout
  @endcontainer
@endsection
