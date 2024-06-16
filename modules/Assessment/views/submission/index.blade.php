@extends('layouts::admin')

@section('head:title') @lang('All Submissions') @endsection
@section('page:title') @lang('All Submissions') @endsection

@section('page:button')
  @can('pages.create')
      @a('Add Submissions', [
        'class' => 'btn btn-primary',
        'icon' => 'mdi mdi-plus',
        'url' => route('submissions.create'),
      ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

        @foreach ($resources as $resource)
          <p>@a($resource->results, ['url' => route('submissions.show', $resource->id)])</p>
          <p>{{ $resource->remarks }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <p>
            @can('submissions.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('submissions.edit', $resource->id),
                ])
            @endcan
          </p>
          <p>
            @can('submissions.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('submissions.destroy', $resource->id),
                ])
            @endcan
          </p>
        @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
