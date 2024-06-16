@extends('layouts::admin')

@section('head:title') @lang('All Threads') @endsection
@section('page:title') @lang('All Threads') @endsection

@section('page:button')
  @can('threads.create')
      @a('Add Threads', [
        'class' => 'btn btn-primary',
        'icon' => 'mdi mdi-plus',
        'url' => route('threads.create'),
      ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

        @foreach ($resources as $resource)
          <p>@a($resource->title, ['url' => route('threads.show', $resource->id)])</p>
          <p>{{ $resource->slug }}</p>
          <p>{{ $resource->body }}</p>
          <p>{{ $resource->author }}</p>
          <p>
            @can('threads.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('threads.edit', $resource->id),
                ])
            @endcan
          </p>
          <p>
            @can('threads.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('threads.destroy', $resource->id),
                ])
            @endcan
          </p>
        @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
