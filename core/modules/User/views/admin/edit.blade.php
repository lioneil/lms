@extends('theme::layouts.admin')

@section('page:back') @back @endsection

@section('page:content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <form action="{{ route('users.update', $resource->id) }}" method="post">
          @csrf
          @method('put')
          @card
            @slot('title')<strong>@lang("Edit {$resource->displayname}")</strong>@endslot

            @slot('body')
              {{ field('firstname')->type('text')->label('First Name')->value($resource->firstname) }}
              {{ field('lastname')->type('text')->label('Last Name')->value($resource->lastname) }}

              {{ field('email')->type('email')->label('Email')->value($resource->email) }}
              {{ field('username')->type('text')->label('Username')->value($resource->username) }}
              {{ field('photo')->type('text')->label('Avatar')->value($resource->avatar) }}
              {{ field('password')->label('Password') }}
              {{-- {{ field('role[]')->label('role') }} --}}
            @endslot
            @slot('footer')
              @submit('Update')
            @endslot
          @endcard
        </form>
      </div>
    </div>
  </div>
@stop
