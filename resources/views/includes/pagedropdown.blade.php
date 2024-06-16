<div class="btn-toolbar" role="toolbar" aria-label="@lang('Toolbar')">
  <button type="button" class="btn btn-icon"><i class="mdi mdi-download"></i></button>
  <div class="dropdown">
    <button class="btn btn-icon dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ request()->get('per_page') ?? settings('display:perpage') }}</button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
      @foreach ($item ?? [5, 10, 15, 20, 50, 100, 'All'] as $i)
        <a class="dropdown-item {{ null === request()->get('per_page') && $i == settings('display:perpage') ? 'active' : null }} {{ $i == request()->get('per_page') ? 'active' : null }}" href="{{ __url(['per_page' => $i, 'page' => null]) }}">{{ $i }}</a>
      @endforeach
    </div>
  </div>
</div>
