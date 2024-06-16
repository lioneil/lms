@stack('before:empty')

@section('empty')

  @flex
    @layout(['attr' => 'row wrap justify-center align-center'])
      <div class="text-xs-center">
        @illustration('empty', [
          'width' => $width ?? '300px',
          'height' => $height ?? '300px',
        ])

        <div class="muted--text mt-4">
          @isset($states['empty']['title'])
            {!! $states['empty']['title'] !!}
          @else
            <h1 class="mb-2">@lang('No resource found')</strong></h1>
          @endisset
          <p>{!! $states['empty']['text'] ?? 'This page returned empty results.' !!}</p>
        </div>

        @if (request()->get('search'))
          <div class="col-lg-6 offset-lg-3 mx-auto text-center">
            @include('Theme::partials.search', ['close' => __('Reload Page')])
          </div>
        @endif
      </div>
    @endlayout
  @endflex
@show

@stack('after:empty')
