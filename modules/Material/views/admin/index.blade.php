@extends('layouts::admin')

@section('head:title') @lang('All Materials') @endsection
@section('page:title') @lang('All Materials') @endsection

@section('page:button')
  @can('materials.create')
    @a('Add Materials', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('materials.create'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

          @foreach ($resources as $resource)
            <p>@a($resource->title, ['url' => route('materials.show', $resource->id)])</p>
            <p>{{ $resource->uri }}</p>
            <p>{{ $resource->author }}</p>
            <p>{{ $resource->created }}</p>
            <p>
              @can('materials.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('materials.edit', $resource->id),
                ])
              @endcan
            </p>
            <p>
              @can('materials.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('materials.destroy', $resource->id),
                ])
              @endcan
            </p>
          @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
