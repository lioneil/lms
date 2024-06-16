@extends('layouts::admin')

@section('head:title') @lang('Preview Course') @endsection
@section('page:title') @lang('Preview Course') @endsection

@section('page:buttons')
  @a('Continue Editing', ['prepend' => 'pencil-outline', 'url' => route('courses.edit', $resource->getKey())])
  @a('Move to Trash', ['prepend' => 'delete-outline', 'url' => route('courses.edit', $resource->getKey())])
@endsection

@section('page:content')
  @container(['attr' => 'grid-list-xl'])
    @layout(['attr' => 'row wrap'])
      @flex(['attr' => 'xs12'])
        @card
          {{-- @slot('featured') {{ $resource->illustration }} @endslot --}}
          @slot('title') @lang($resource->title) @endslot
          @slot('body')
            {{ $resource->subtitle }}
            {!! $resource->illustration !!}
            <hr>
            {{ $resource->overview }}
            @foreach ($resource->lessons as $lesson)
              {{ $lesson->title }}
              {{ $lesson->subtitle }}
            @endforeach
          @endslot
        @endcard
      @endflex
    @endlayout
  @can('courses.edit', $resource)
    @lang('Continue Editing')
    {{-- @edit(['url' => route('courses.edit', $resource->getKey())])
      @slot('button')
        @button('Edit Course')
      @endslot
    @endedit --}}
  @endcan
  @can('courses.destroy', $resource)
    Move to Trash
    <form action="{{ route('courses.destroy', $resource->getKey()) }}" method="post">
      @csrf
      @method('delete')
      @submit('Move to Trash')
    </form>
  @endcan
  @can('courses.publish', $resource)
    Publish
  @endcan
@endsection

@section('page:content')
  @container
    @row
      @column
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
          @endforeach
        </ol>

      @endcolumn
    @endrow
  @endcontainer
@stop
