@foreach ($menus as $submenu)
  @can($submenu->key('permissions'))
    @if ($submenu->is('divider'))
      <v-divider></v-divider>
    @else
      <v-list-tile
        href="{{ $submenu->url() }}"
        title="@lang($submenu->description())"
        value="{{ $submenu->active() }}"
        >
        <v-list-tile-action>
          @if ($submenu->icon())
            @icon($submenu->icon())
          @endif
        </v-list-tile-action>
        @if ($submenu->text())
          <v-list-tile-content>
            <v-list-tile-title>
              @lang($submenu->text())
            </v-list-tile-title>
          </v-list-tile-content>
        @endif
        @if ($submenu->has('badge'))
          @badge($submenu->badge())
        @endif
      </v-list-tile>
    @endif
  @endcan
@endforeach
