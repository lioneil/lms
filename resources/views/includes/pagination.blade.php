{{-- @php($resources->setPath('')->appends(request()->input())) --}}

@if ($resources->lastPage() > 1)
  <ul class="pagination">
    {{-- << button --}}
    <li class="page-item mr-3 {{ $resources->onFirstPage() ? 'disabled' : null }}">
      <a {{ $resources->onFirstPage() ? 'disabled' : null }} class="page-link rounded" href="{{ __url(['page' => 1]) }}{{ $section ?? '' }}" aria-label="@lang('First')">
        <span aria-hidden="true">&laquo;</span>
        <span class="sr-only">@lang('First')</span>
      </a>
    </li>
    {{-- << button --}}

    {{-- < previous button --}}
    <li class="page-item mr-3 {{ $resources->currentPage() == 1 ? 'disabled' : null }}">
      <a {{ $resources->currentPage() == 1 ? 'disabled' : null }} class="page-link rounded" href="{{ $resources->previousPageUrl() }}{{ $section ?? '' }}" aria-label="@lang('Previous')">
        <span aria-hidden="true">&lsaquo;</span>
        <span class="sr-only">@lang('Previous')</span>
      </a>
    </li>
    {{-- < previous button --}}

    {{-- page loop --}}
    @for ($i = 1; $i <= $resources->lastPage(); $i++)
      <li class="page-item mr-3 {{ $resources->currentPage() == $i ? 'active' : '' }}">
        <a class="page-link rounded" href="{{ __url(['page' => $i]) }}{{ $section ?? '' }}">
          {{ $i }}
          @if ($resources->currentPage() == $i)<span class="sr-only">(@lang('current'))</span>@endif
        </a>
      </li>
    @endfor
    {{-- page loop --}}

    {{-- > next button --}}
    <li class="page-item mr-3 {{ ! $resources->hasMorePages() ? 'disabled' : null }}">
      <a {{ ! $resources->hasMorePages() ? 'disabled' : null }} class="page-link rounded" href="{{ $resources->nextPageUrl() }}{{ $section ?? '' }}" aria-label="@lang('Next')">
        <span aria-hidden="true">&rsaquo;</span>
        <span class="sr-only">@lang('Next')</span>
      </a>
    </li>
    {{-- > next button --}}

    {{-- >> last button --}}
    <li class="page-item {{ ! $resources->hasMorePages() ? 'disabled' : null }}">
      <a {{ ! $resources->hasMorePages() ? 'disabled' : null }} class="page-link rounded" href="{{ __url(['page' => $resources->lastPage()]) }}{{ $section ?? '' }}" aria-label="@lang('Last')">
        <span aria-hidden="true">&raquo;</span>
        <span class="sr-only">@lang('Last')</span>
      </a>
    </li>
    {{-- >> last button --}}
  </ul>
@endif
