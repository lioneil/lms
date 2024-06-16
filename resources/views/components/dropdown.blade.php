<div class="dropdown">
  <button id="dropdown-menu-{{ $t = time() }}" class="btn {{ $class ?? 'btn-link' }} dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    @isset($icon)<i class="{{ $icon }}"></i>@endisset
    {{ $button }}
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdown-menu-{{ $t }}">
    {{ $slot }}
  </div>
</div>
