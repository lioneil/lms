@extends('theme::layouts.admin')

@section('page:back') @back(route('users.index')) @endsection

@section('page:content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">

        @card
          @slot('body')
            <div class="row">
              <div class="col-auto">
                @avatar($resource->avatar, ['class' => 'avatar-lg'])
              </div>
              <div class="col">
                <h3 class="card-title mb-4">{{ $resource->displayname }}</h3>
                <div class="sub-details">
                  <p>@icon('mdi mdi-email'){{ $resource->email }}</p>
                  <p>@icon('mdi mdi-at'){{ $resource->username }}</p>
                </div>
              </div>
            </div>
          @endslot

          @table(['class' => 'table-borderless table-card mb-0'])
            @self($resource)
              @slot('thead')
                <tr>
                  <th colspan="100%">@lang('Account')</th>
                </tr>
              @endslot
              @slot('tbody')
                @section('user:account')
                  @if ($resource->getDetailsOfType('account')->isEmpty())
                    <tr>
                      <td class="text-muted small">
                        @lang('Account information is either hidden or it does not exist.')
                        @if ($resource->isSuperAdmin())
                          <a href="#" target="_blank">@lang('Learn more about security')</a>
                        @endif
                      </td>
                    </tr>
                  @endif
                  @foreach ($resource->getDetailsOfType('account') as $account)
                    <tr>
                      <td><i class="{{ $account->icon }}">&nbsp;</i><strong>@lang(ucfirst($account->key))</strong></td>
                      @if ($account->isPasswordField())
                        <td>{{ $account->password }} @icon('mdi mdi-eye-outline')</td>
                      @else
                        <td>{{ $account->value }}</td>
                      @endif
                    </tr>
                  @endforeach
                @show
              @endslot
            @endself

            @if ($resource->getRemainingDetails()->isNotEmpty())
              <thead class="thead-light">
                <tr><th colspan="100%">@lang('Details')</th></tr>
              </thead>
              <tbody>
                @foreach ($resource->getRemainingDetails() as $detail)
                  <tr>
                    <td><strong>@lang($detail->key)</strong></td>
                    <td>@lang($detail->value)</td>
                  </tr>
                @endforeach
              </tbody>
            @endif
          @endtable
        @endcard

        @card
          @slot('body')
            {{--  --}}
          @endslot
        @endcard

      </div>
    </div>
  </div>
@endsection
