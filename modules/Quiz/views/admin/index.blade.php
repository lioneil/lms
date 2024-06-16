@extends('layouts::admin')

@section('head:title') @lang('All Quizzes') @endsection
@section('page:title') @lang('All Quizzes') @endsection

@section('page:button')
  @can('quizes.create')
    @a('Add Quizzes', [
      'class' => 'btn btn-primary',
      'icon' => 'mdi mdi-plus',
      'url' => route('quizzes.create'),
    ])
  @endcan
@endsection

@section('page:content')
  <v-container fluid grid-list-content>
    <v-row wrap>
      <v-flex xs12>

          @foreach ($resources as $resource)
            <p>@a($resource->title, ['url' => route('quizzes.show', $resource->id)])</p>
            <p>{{ $resource->description }}</p>
            <p>
              @can('quizes.edit')
                @a('Edit', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('quizzes.edit', $resource->id),
                ])
              @endcan
            </p>
            <p>
              @can('quizes.destroy')
                @a('Move to Trash', [
                  'icon' => 'mdi mdi-pencil-outline',
                  'url' => route('quizzes.destroy', $resource->id),
                ])
              @endcan
            </p>
          @endforeach

      </v-flex>
    </v-row>
  </v-container>
@endsection

