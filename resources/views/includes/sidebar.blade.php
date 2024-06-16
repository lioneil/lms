<v-navigation-drawer
  app
  light
  overflow
  >
  <v-toolbar flat class="transparent">
    <v-list class="pa-0">
      <v-list-tile avatar title="{{ settings('app:tagline') }}">
        <v-list-tile-avatar>
          @brand(url('logo.png'))
        </v-list-tile-avatar>
        <v-list-tile-content>
          <v-list-tile-title>{{ settings('app:title') }}</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
    </v-list>
  </v-toolbar>

  <v-list>
    @foreach (sidebar()->all() ?? [] as $i => $menu)
      @if ($menu->is('header'))
        <v-list-tile
          title="@lang($menu->description())"
          >
          @if ($menu->icon())
            <v-list-tile-action>
              @icon($menu->icon())
            </v-list-tile-action>
          @endif
          @if ($menu->text())
            <v-list-tile-title>
              @lang($menu->text())
            </v-list-tile-title>
          @endif
        </v-list-tile>

      @elseif ($menu->hasChild())
        @can($menu->key('permissions'))
          <v-list-group value="{{ $menu->active() }}"
            >
            <template v-slot:activator>
              <v-list-tile
                title="@lang($menu->description())"
                value="{{ $menu->active() }}"
                >
                @if ($menu->icon())
                  <v-list-tile-action>
                    @icon($menu->icon())
                  </v-list-tile-action>
                @endif
                @if ($menu->text())
                  <v-list-tile-content>
                    <v-list-tile-title>
                      @lang($menu->text())
                    </v-list-tile-title>
                  </v-list-tile-content>
                @endif
                @if ($menu->has('badge'))
                  @chip($menu->badge())
                @endif
              </v-list-tile>
            </template>

            @if ($menu->hasChild())
              @sidebarmenus([
                'menus' => $menu->children()
              ])
            @endif
          </v-list-group>
        @endcan

      @else
        @can($menu->key('permissions'))
          <v-list-tile
            href="{{ $menu->url() }}"
            title="@lang($menu->description())"
            value="{{ $menu->active() }}"
            >
            <v-list-tile-action>
              @icon($menu->icon())
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>
                @lang($menu->text())
              </v-list-tile-title>
            </v-list-tile-content>
            @if ($menu->has('badge'))
              <v-list-tile-action>
                @chip($menu->badge())
              </v-list-tile-action>
            @endif
          </v-list-tile>
        @endcan
      @endif
    @endforeach

  </v-list>
</v-navigation-drawer>
