@extends('layouts::admin')

@section('head:title') @lang('All Courses') @endsection
@section('page:title') @lang('All Courses') @endsection

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
  @container(['attr' => 'grid-list-xl'])
    @layout(['attr' => 'row wrap'])
      @flex(['attr' => 'xs12'])

        @foreach ($resources as $resource)
          <p>@a($resource->title, ['url' => route('courses.show', $resource->id)])</p>
          <p>{{ $resource->subtitle }}</p>
          <p>{{ $resource->code }}</p>
          <p>{{ $resource->status }}</p>
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

      @endflex
    @endlayout
  @endcontainer
@stop
