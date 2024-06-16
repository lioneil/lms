@toolbar(['attr' => 'flat', 'class' => "white"])
  @slot('button')
    @menu(['attr' => 'offset-y'])
      @slot('items')
        @slot('activator')
          @card(['attr' => 'flat', 'class' => 'transparent pa-2'])
            @slot('footer')
              @avatar(user()->avatar, ['class' => 'pr-4'])
              <div>
                <p class="caption mb-0">{{ user()->displayname }}</p>
                <p class="caption mb-0 grey--text text--darken-2">{{ user()->role }}</p>
              </div>
            @endslot
          @endcard
        @endslot
        @card(['attr' => 'flat'])
          @list(['attr' => 'flat dense', 'url' => route('logout.logout')])
            @slot('avatar')
              @icon('exit-to-app', ['class' => 'grey--text'])
            @endslot
            @slot('content')
              @slot('title')
                @lang('Logout')
              @endslot
            @endslot
          @endlist
        @endcard
      @endslot
    @endmenu
  @endslot
@endtoolbar
