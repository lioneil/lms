@extends('theme::layouts.admin')

@section('page')
  <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
    @csrf

    @header
      @slot('title')@lang('Add User')@endslot
      @slot('button')
        @submit('Save', ['icon' => 'mdi mdi-content-save'])
      @endslot
    @endheader

    <div class="card mb-3">
      <div class="card-header">{{ __('Personal Information') }}</div>
      <div class="card-body">
        <div class="row wrap">
          <div class="col-md-4">
            {{ field('firstname')->type('text')->label('First name')->autofocus() }}
          </div>
          <div class="col-md-3">
            {{ field('middlename')->type('text')->label('Middle name') }}
          </div>
          <div class="col-md-3">
            {{ field('lastname')->type('text')->label('Last name') }}
          </div>
          <div class="col-md-2">
            {{ field('suffixname')->type('text')->label('Suffix')->placeholder('e.g. Jr.') }}
          </div>
        </div>

        <div class="row wrap">
          <div class="col-lg-12">
            {{ field('email')->type('email')->label('Email')->placeholder('e.mail@domain.io')->style('width: 300px') }}
            {{ field('username')->type('text')->label('Username')->placeholder('Username')->style('width: 300px') }}
            {{ field('password')->type('text')->label('Password')->placeholder('Password') }}
          </div>
        </div>
      </div>
    </div>

  </form>
@endsection
