@extends('layouts::admin')

@section('page:title')@lang('All Users')@endsection

@section('page:button')
  @can('users.create')
    @a('Add User', ['icon' => 'mdi mdi-account-plus', 'class' => 'btn btn-primary', 'url' => route('users.create')])
  @endcan
@endsection

@section('page:buttons')
  <div class="btn-toolbar mb-3" role="toolbar" aria-label="@lang('Button toolbar')">
    @a('Import', ['url' => "route('users.import')", 'class' => 'btn btn-link small mr-3', 'icon' => 'mdi mdi-upload'])
    @button('Export All', ['class' => 'btn btn-link small mr-3', 'icon' => 'mdi mdi-download'])
    @a('View Deactivate Users', ['url' => route('users.trashed'), 'class' => 'btn btn-link small mr-3', 'icon' => 'mdi mdi-delete-outline'])
    @can('roles.index')
      @a('Roles', ['url' => route('roles.index'), 'class' => 'btn btn-link small mr-3', 'icon' => 'mdi mdi-shield-account-outline'])
    @endcan
    @can('permissions.index')
      @a('Permissions', ['url' => route('permissions.index'), 'class' => 'btn btn-link small mr-3', 'icon' => 'mdi mdi-shield-key-outline'])
    @endcan
  </div>
@endsection

@section('page:content')
  @tablecard(['class' => 'table-borderless'])
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
          <th colspan="2">@sort(['label' => 'Name', 'key' => 'firstname'])</th>
          <th>@sort(['label' => 'Email', 'key' => 'email'])</th>
          <th>@sort(['label' => 'Role', 'key' => 'position'])</th>
          <th>@sort(['label' => 'Joined', 'key' => 'created_at'])</th>
          <th class="text-center">@lang('Actions')</th>
        </tr>
      @endslot
      @slot('tbody')
        @foreach ($resources as $resource)
          <tr>
            <td>@avatar($resource->avatar)</td>
            <td>
              @a($resource->displayname, ['url' => route('users.show', $resource->id), 'class' => 'text-link'])
              <div class="d-md-none">
                @can('users.edit')
                  @edit(['text' => 'Edit', 'url' => route('users.edit', $resource->id)])
                @endcan

                @can('users.destroy')
                  @button('Deactivate', [
                    'icon' => 'mdi mdi-delete-outline',
                    'url' => null,
                    'attr' => 'data-toggle=modal data-target=#modal-destroy-'.$resource->id
                  ])
                @endcan
              </div>
            </td>
            <td>@a($resource->email, ['url' => "mailto:{$resource->email}", 'class' => false])</td>
            <td>@lang($resource->role)</td>
            <td>@lang($resource->joined)</td>
            <td class="text-center">
              @can('users.show')
                @preview(['text' => 'Preview', 'url' => route('users.show', $resource->id)])
              @endcan
              @can('users.edit')
                @edit(['text' => 'Edit', 'url' => route('users.edit', $resource->id)])
              @endcan
              @can('users.destroy')
                @trash(['id' => 'modal-destroy-'.$resource->id, 'url' => route('users.destroy', $resource->id)])
                  @slot('title')
                    @lang('Deactivate Account')
                  @endslot
                  @slot('button')
                    @button('Deactivate', [
                      'icon' => 'mdi mdi-delete-outline',
                      'url' => null,
                      'class' => 'btn btn-link mx-3',
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
                      <p class="text-muted">@lang(sprintf('You may restore the account from %s.', '<a target="_blank" href="'.route('users.trashed').'">Deactivated Users</a>'))</p>
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
  @endtablecard
@stop
