@extends('layouts::admin')

@section('head:title') @lang('All Categories') @endsection
@section('page:title') @lang('All Categories') @endsection

@section('page:button')
  @can('categories.create')
    @a('Add Announcement', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('categories.create'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

          @foreach ($resources as $resource)
            <p>@a($resource->name)</p>
            <p>@a($resource->code)</p>
            <p>
              @can('categories.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('categories.edit', $resource->id),
                ])
              @endcan
            </p>
            <p>
              @can('categories.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('categories.destroy', $resource->id),
                ])
              @endcan
            </p>
          @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
