<div class="table-responsive">
  <table class="table {{ $class ?? null }}">
    @isset($thead)<thead class="thead-light">{{ $thead ?? null }}</thead>@endisset
    <tbody>{{ $tbody ?? null }}</tbody>
    @isset($tfoot)<tfoot>{{ $tfoot ?? null }}</tfoot>@endisset
    {{ $slot }}
  </table>
</div>
