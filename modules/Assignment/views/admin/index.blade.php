@extends('layouts::admin')

@section('head:title') @lang('All Assignments') @endsection
@section('page:title') @lang('All Assignments') @endsection

@section('page:button')
  @can('assignments.create')
    @a('Add Assignments', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('assignments.create'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

          @foreach ($resources as $resource)
            <p>@a($resource->title, ['url' => route('assignments.show', $resource->id)])</p>
            <p>{{ $resource->uri }}</p>
            <p>{{ $resource->author }}</p>
            <p>{{ $resource->created }}</p>
            <p>
              @can('assignments.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('assignments.edit', $resource->id),
                ])
              @endcan
            </p>
            <p>
              @can('assignments.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('assignments.destroy', $resource->id),
                ])
              @endcan
            </p>
          @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
