@extends('layouts::admin')

@section('head:title') @lang('All Announcements') @endsection
@section('page:title') @lang('All Announcements') @endsection

@section('page:button')
  @can('announcements.create')
    @a('Add Announcement', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('announcements.create'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

          @foreach ($resources as $resource)
            <p>@a($resource->title, ['url' => route('announcements.show', $resource->id)])</p>
            <p>@a($resource->name)</p>
            <p>@a($resource->code)</p>
            <p>
              @can('announcements.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('announcements.edit', $resource->id),
                ])
              @endcan
            </p>
            <p>
              @can('announcements.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('announcements.destroy', $resource->id),
                ])
              @endcan
            </p>
          @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
