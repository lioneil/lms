@extends('layouts::admin')

@section('head:title') @lang('All Settings') @endsection
@section('page:title') @lang('All Settings') @endsection

@section('page:button')
  @can('settings.create')
    @a('Add Settings', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('settings.store'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

          @foreach ($settings as $set)
            <p>{{ $set->key }}</p>
            <p>{{ $set->value }}</p>
            <p>
              @can('settings.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline'
                ])
              @endcan
            </p>
          @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
