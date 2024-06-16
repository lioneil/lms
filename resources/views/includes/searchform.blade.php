<form action="{{ __url() }}" class="form-inline" method="GET" autocomplete="off">
  <div class="form-row">
    <div class="col">
      <input type="search" name="search" class="form-control" placeholder="@lang('Search ctrl+f')" value="{{ request()->get('search') }}">
    </div>
    <div class="col">
      <button type="submit" class="btn btn-secondary"><i class="mdi mdi-magnify"></i></button>
    </div>
  </div>
</form>
