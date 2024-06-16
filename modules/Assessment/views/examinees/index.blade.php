@extends('layouts::admin')

@section('head:title') @lang('All Examinees') @endsection
@section('page:title') @lang('All Examinees') @endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

        @foreach ($resources as $resource)
          <p>{{ $resource->title }}</p>
          <p>{{ $resource->code }}</p>
          <p>{{ $resource->type }}</p>
          <p>{{ $resource->created }}</p>
        @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection
