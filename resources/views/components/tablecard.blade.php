<div class="card mb-3">
  @isset($header)<div class="card-header justify-content-between">{{ $header }}</div>@endisset
  <div class="table-responsive">
    <table class="table table-card {{ $class ?? null }}">
      <thead class="thead-light">{{ $thead ?? null }}</thead>
      <tbody>{{ $tbody ?? null }}</tbody>
      <tfoot>{{ $tfoot ?? null }}</tfoot>
    </table>
  </div>
  {{ $slot }}
  @isset($footer)<div class="card-footer">{{ $footer }}</div>@endisset
</div>
