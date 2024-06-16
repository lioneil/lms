@extends('layouts::settings')

@section('page:title')@lang('Settings')@endsection

@section('page:content')

  <form action="{{ route('settings.store') }}" method="post">
    @csrf

    <div class="row">
      <div class="col-md-8">
        {{ field('app:title')->label('Application Title')->value(settings('app:title')) }}
        {{ field('app:tagline')->label('Application Subtitle')->value(settings('app:tagline')) }}
      </div>
      <div class="col-md-4">
        {{ field('app:logo')->label('Logo')->type('file') }}
        {{ field('app:logo')->value(settings('app:logo'))->type('hidden') }}
      </div>
    </div>
    {{ field('app:author')->label('Application Author')->value(settings('app:author')) }}
    {{ field('app:email')->label('Application Email')->value(settings('app:email')) }}
    {{ field('app:year')->label('Application Year')->value(settings('app:year')) }}

    @submit('Save Options')

  </form>

@endsection
