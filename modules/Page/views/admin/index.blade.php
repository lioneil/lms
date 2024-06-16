@extends('layouts::admin')

@section('head:title') @lang('All Pages') @endsection
@section('page:title') @lang('All Pages') @endsection

@section('page:button')
  @can('pages.create')
      @a('Add Pages', [
        'class' => 'btn btn-primary',
        'icon' => 'mdi mdi-plus',
        'url' => route('pages.create'),
      ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

        @foreach ($resources as $resource)
          <p>@a($resource->title, ['url' => route('pages.show', $resource->id)])</p>
          <p>{{ $resource->code }}</p>
          <p>{{ $resource->feature }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <p>
            @can('pages.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('pages.edit', $resource->id),
                ])
            @endcan
          </p>
          <p>
            @can('pages.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('pages.destroy', $resource->id),
                ])
            @endcan
          </p>
        @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
