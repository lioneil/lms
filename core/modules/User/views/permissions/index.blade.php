@extends('layouts::admin')

@section('page:title')@lang('All Permissions')@endsection

@section('page:buttons')
  <div class="btn-toolbar mb-3" permission="toolbar" aria-label="@lang('Button toolbar')">
    @can('permissions.refresh')
      @refresh(['id' => 'modal-refresh-all', 'url' => route('permissions.refresh')])
        @slot('title')@lang('Refresh Permissions')@endslot
        @slot('button')
          @button('Refresh', [
            'url' => '#',
            'class' => 'btn btn-link mr-3',
            'icon' => 'mdi mdi-refresh',
            'attr' => 'data-toggle=modal data-target=#modal-refresh-all',
          ])
        @endslot
      @endrefresh
    @endcan
  </div>
@endsection

@section('page:content')
  @tablecard(['class' => 'table-borderless'])
    @slot('tbody')
      @foreach ($resources->groupBy('group') as $name => $group)
        <tr>
          <th colspan="100%" class="small">{{ $name }}</th>
        </tr>
        <tr>
          <th>@lang('Name')</th>
          <th>@lang('Code')</th>
          <th>@lang('Description')</th>
        </tr>
        @foreach ($group as $permission)
          <tr>
            <td>{{ $permission->name }}</td>
            <td>{{ $permission->code }}</td>
            <td>{{ $permission->description }}</td>
          </tr>
        @endforeach
      @endforeach
    @endslot
  @endtablecard
  {{-- @tablecard(['class' => 'table-borderless'])
    @slot('header')
      @searchform
      @pagedropdown
    @endslot

    @if ($resources->isEmpty())
      @svg('theme::icons.deactivate')
      <p class="text-muted text-center mb-4">@lang('No items found.')</p>
    @endif

    @if ($resources->isNotEmpty())
      @slot('thead')
        <tr>
          <th>@sort(['label' => 'Name', 'key' => 'name'])</th>
          <th>@sort(['label' => 'Alias', 'key' => 'alias'])</th>
          <th>@sort(['label' => 'Code', 'key' => 'code'])</th>
          <th>@sort(['label' => 'Permissions', 'key' => 'status'])</th>
          <th colspan="2" class="text-center">@lang('Actions')</th>
        </tr>
      @endslot
      @slot('tbody')
        @foreach ($resources as $resource)
          <tr>
            <td>
              @a($resource->name, ['url' => route('permissions.show', $resource->id), 'class' => 'text-link'])
              <div class="d-md-none">
                @can('permissions.edit')
                  @edit(['text' => 'Edit', 'url' => route('permissions.edit', $resource->id)])
                @endcan

                @can('permissions.destroy')
                  @button('Move to Trash', [
                    'icon' => 'mdi mdi-delete-outline',
                    'url' => null,
                    'attr' => 'data-toggle=modal data-target=#modal-destroy-'.$resource->id
                  ])
                @endcan
              </div>
            </td>
            <td>@lang($resource->alias)</td>
            <td>@lang($resource->code)</td>
            <td>@lang($resource->status)</td>
            <td>
              @can('permissions.edit')
                @edit(['text' => 'Edit', 'url' => route('permissions.edit', $resource->id)])
              @endcan
            </td>
            <td>
              @can('permissions.destroy')
                @trash(['id' => 'modal-destroy-'.$resource->id, 'url' => route('permissions.destroy', $resource->id)])
                  @slot('title')
                    @lang('Deactivate Account')
                  @endslot
                  @slot('button')
                    @button('Move to Trash', [
                      'icon' => 'mdi mdi-delete-outline',
                      'url' => null,
                      'attr' => 'data-toggle=modal data-target=#modal-destroy-'.$resource->id
                    ])
                  @endslot
                  <div class="row">
                    <div class="col-auto">
                      @avatar($resource->avatar, ['width' => '80px'])
                    </div>
                    <div class="col">
                      <p class="lead">{{ sprintf(__('%s account will be moved to trash.'), $resource->displayname) }}</p>
                      <p>@lang('Are you sure you want to continue with the action?')</p>
                      <p class="text-muted">@lang(sprintf('You may reactivate the account from %s.', '<a target="_blank" href="'.route('permissions.trashed').'">Deactivated Users</a>'))</p>
                    </div>
                  </div>
                  @slot('submit')
                    @submit('Deactivate', ['context' => 'warning'])
                  @endslot
                @endtrash
              @endcan
            </td>
          </tr>
        @endforeach
      @endslot
      @slot('footer')
        @pagination($resources)
      @endslot
    @endif
  @endtablecard --}}
@stop
