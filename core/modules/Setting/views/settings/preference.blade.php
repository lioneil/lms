@extends('layouts::settings', ['setting' => 'settings:preferences'])

@section('page:title')@lang('Settings')@endsection

@section('form:content')

  <h4 class="h5 mb-3"><strong>@lang('Preferences')</strong></h4>

  <div class="row">
    <div class="col-md-8">
      {{-- {{ field('format:date')->label('Date Format')->value(settings('format:date')) }}
      {{ field('format:time')->label('Time Format')->value(settings('format:time')) }}
      {{ field('format:datetime')->label('Date & Time Format')->value(settings('format:datetime')) }}
      {{ field('display:perpage')->label('Display Items per Page')->value(settings('display:perpage')) }} --}}
    </div>
  </div>

@endsection
