@extends('layouts::admin')

@section('head:title') @lang('My Courses') @endsection
@section('page:title') @lang('My Courses') @endsection

@section('page:button')
  @can('courses.create')
    @a('Add Course', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('courses.create'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

        @foreach ($resources as $resource)
          <p>@lang($resource->title)</p>
          <p>{{ $resource->code }}</p>
          <p>{{ $resource->author }}</p>
          <p>{{ $resource->created }}</p>
          <p>
            @can('courses.edit')
              @a('Edit', [
                'icon' => 'mdi mdi-pencil-outline',
                'url' => route('courses.edit', $resource->id),
              ])
            @endcan
          </p>
          <p>
            @can('courses.destroy')
              @a('Move to Trash', [
                'icon' => 'mdi mdi-delete-outline',
                'url' => route('courses.destroy', $resource->id),
              ])
            @endcan
          </p>
        @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
