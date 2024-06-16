@extends('layouts::settings', ['setting' => 'module:mail'])

@section('head:title', __('Mails'))
@section('page:title', __('Mails'))

@section('page:content')

  <div class="row">
    <div class="col-lg-6 mx-auto">

      @foreach ($resources as $resource)
        @card
          @slot('body')
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ $resource->getName() }}</span>
              @a('Show', ['url' => route('mails.show', $resource->getName()), 'icon' => 'mdi mdi-eye-outline'])
            </div>
          @endslot
        @endcard
      @endforeach

    </div>
  </div>

@endsection
