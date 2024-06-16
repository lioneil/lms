@extends('layouts::public')

@section('head:title') @lang($resource->title) @endsection
@section('page:title') @lang($resource->title) @endsection

@section('page:content')
  @container
    bread
    @breadcrumbs
    bread
    <hr>

    @row
      @column
        @auth
          @can('courses.favorite')
            form to favorite a course.
            Favorite
          @endcan
        @endauth
        {{-- ILLUSTRATION --}}
        {{-- @svg($resource->illustration) --}}
        {!! $resource->illustration !!}
        {{-- ILLUSTRATION --}}

        <small>@icon($resource->category->icon) @lang($resource->category->name)</small>
        <h1 class="page-title">{{ $resource->title }}</h1>
        <h1 class="page-title">{{ $resource->subtitle }}</h1>
        <h6>{{ $resource->code }}</h6>
        @chip($resource->status)

        <h1>@lang('Overview')</h1>
        <div class="page-description">
          {{ $resource->overview }}
        </div>

        {{-- AUTHOR --}}
        <div>
          {{--
            Proposal:
            @author($resource)
            --}}
          <em>@lang('By')</em>
          @avatar($resource->user->avatar)
          @lang($resource->author)
        </div>
        {{-- AUTHOR --}}

        <p>TAGS</p>
        @foreach ($resource->tags as $tag)
          @chip($tag->name)
        @endforeach
        <p>TAGS</p>

        {{-- @subtitle($resource->subtitle) --}}
        <p>{{ $resource->subtitle }}</p>

        <h2>@lang('Lessons')</h2>
        <ol>
          @foreach ($resource->lessons as $lesson)
            <li>@lang($lesson->title) || @lang($lesson->slug)</li>
            <li>@lang($lesson->subtitle)</li>
            <li>@lang($lesson->excerpt)</li>
          @endforeach
        </ol>

      @endcolumn
    @endrow
  @endcontainer
@stop
