@card
  @slot('body')
    @avatar($user->avatar)
    Logged in: {{ $user->displayname }}
  @endslot
@endcard
