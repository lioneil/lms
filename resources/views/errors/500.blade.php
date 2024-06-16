@failure
  @slot('head')
    @lang('500 Internal Server')
  @endslot

  @slot('title')
    @lang('500 Error')
  @endslot

  @slot('subtitle')
    @lang('Oops! Something went wrong')
  @endslot

  @slot('message')
    @lang('We are experiencing an internal server problem. Please try back later.')
  @endslot

  @slot('button')
    @button(['href' => route('dashboard')]) @lang('Home') @endbutton
  @endslot
@endfailure
