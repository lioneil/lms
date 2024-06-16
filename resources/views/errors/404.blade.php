@failure
  @slot('head')
    @lang('404 Page Not Found')
  @endslot

  @slot('title')
    @lang('404 Error')
  @endslot

  @slot('subtitle')
    @lang("There's no page here 😭")
  @endslot

  @slot('message')
    @lang('Looks like you ended up here by accident?')
  @endslot

  @slot('button')
    @button(['href' => route('dashboard')]) @lang('Return to your Dashboard') @endbutton
  @endslot

  @slot('superadminsonly') @endslot
@endfailure
