@extends('layouts::admin')

@section('head:title') @lang('All Libraries') @endsection
@section('page:title') @lang('All Libraries') @endsection

@section('page:button')
  @can('libraries.create')
    @a('Add Library', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('libraries.create'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

          @foreach ($resources as $resource)
            <p>@a($resource->name, ['url' => route('libraries.show', $resource->id)])</p>
            <p>{{ $resource->url }}</p>
            <p>{{ $resource->author }}</p>
            <p>{{ $resource->created }}</p>
            <p>
              @can('libraries.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('libraries.edit', $resource->id),
                ])
              @endcan
            </p>
            <p>
              @can('libraries.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('libraries.destroy', $resource->id),
                ])
              @endcan
            </p>
          @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
