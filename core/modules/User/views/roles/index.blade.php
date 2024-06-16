@extends('layouts::admin')

@section('page:title')@lang('All Roles')@endsection

@section('page:button')
  @can('roles.create')
    @a('Add Role', ['icon' => 'mdi mdi-account-multiple-check', 'class' => 'btn btn-primary', 'url' => route('roles.create')])
  @endcan
@endsection

@section('page:buttons')
  <div class="btn-toolbar mb-3" role="toolbar" aria-label="@lang('Button toolbar')">
    @can('roles.import')
      @dropdown(['class' => 'btn-link mr-3', 'icon' => 'mdi mdi-upload'])
        @slot('button')@lang('Import')@endslot
        {{-- Import from File --}}
        @modal(['id' => 'modal-import-from-file', 'url' => route('roles.upload'), 'upload' => true])
          @method('put')
          @slot('title')@lang('Import From File')@endslot
          @slot('button')
            @a('Import from file...', [
              'url' => '#',
              'class' => 'dropdown-item',
              'icon' => 'mdi mdi-cloud-upload',
              'attr' => 'data-toggle=modal data-target=#modal-import-from-file',
            ])
          @endslot
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-10 mx-auto">
                @svg('theme::icons.accept', ['context' => 'primary', 'height' => '200px', 'class' => 'mb-3'])
                <input type="file" name="roles">
              </div>
            </div>
          </div>
          @slot('save')
            @submit('Import', ['context' => 'primary'])
          @endslot
        @endmodal
        {{-- Import from File --}}

        <div class="dropdown-divider"></div>

        {{-- Install Defaults --}}
        @modal(['id' => 'modal-install-defaults', 'url' => route('roles.import')])
          @method('put')
          @slot('title')@lang('Install Default Roles')@endslot
          @slot('button')
            @a('Install defaults...', [
              'url' => '#',
              'class' => 'dropdown-item',
              'icon' => 'mdi mdi-apps',
              'attr' => 'data-toggle=modal data-target=#modal-install-defaults',
            ])
          @endslot
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-10 mx-auto">
                @svg('theme::icons.accept', ['context' => 'primary', 'height' => '200px', 'class' => 'mb-3'])
                <p>@lang('Roles allow you to manage user acces on each page or action. Some Modules will have a pre-configured role or set of roles. By default, these roles are not installed.')</p>
                <p>@lang('By continuing, the application will try to install the roles declared in each modules, if any.')</p>
                <p>@lang("After the installation, you may have to setup or customize the role's permissions.")</p>
                @treeview('start')
                @foreach ($service->defaults() as $role)
                  <div>
                    <input type="checkbox" name="roles[]" value="{{ $role['code'] }}">
                    @lang($role['name'])
                  </div>
                @endforeach
                @treeview('end')
              </div>
            </div>
          </div>
          @slot('save')
            @submit('Import', ['context' => 'primary'])
          @endslot
        @endmodal
        {{-- Install Defaults --}}
      @enddropdown
    @endcan

    @can('roles.export')
      @button('Export All', ['class' => 'btn btn-link small mr-3', 'icon' => 'mdi mdi-download'])
    @endcan

    @can('roles.trashed')
      @a('View Trashed Roles', ['url' => route('roles.trashed'), 'class' => 'btn btn-link small mr-3', 'icon' => 'mdi mdi-delete-outline'])
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
          <th>@sort(['label' => 'Name', 'key' => 'name'])</th>
          <th>@sort(['label' => 'Alias', 'key' => 'alias'])</th>
          <th>@sort(['label' => 'Code', 'key' => 'code'])</th>
          <th>@sort(['label' => 'Permissions', 'key' => 'status'])</th>
          <th colspan="1" class="text-center">@lang('Actions')</th>
        </tr>
      @endslot
      @slot('tbody')
        @foreach ($resources as $resource)
          <tr>
            <td>
              @a($resource->name, ['url' => route('roles.show', $resource->id), 'class' => 'text-link'])
              <div class="d-md-none">
                @can('roles.edit')
                  @edit(['text' => 'Edit', 'url' => route('roles.edit', $resource->id)])
                @endcan

                @can('roles.destroy')
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
            <td class="text-center">
              @can('roles.show')
                <span class="mr-3">@preview(['text' => 'Preview', 'url' => route('roles.show', $resource->id)])</span>
              @endcan
              @can('roles.edit')
                <span class="mr-3">@edit(['text' => 'Edit', 'url' => route('roles.edit', $resource->id)])</span>
              @endcan
              @can('roles.destroy')
                @trash(['id' => 'modal-destroy-'.$resource->id, 'url' => route('roles.destroy', $resource->id)])
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
                      <p class="text-muted">@lang(sprintf('You may reactivate the account from %s.', '<a target="_blank" href="'.route('roles.trashed').'">Deactivated Users</a>'))</p>
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
