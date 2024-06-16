@failure
  @slot('head')
    @lang('403 Forbidden Error')
  @endslot

  @slot('title')
    @lang('403 Error')
  @endslot

  @slot('subtitle')
    @lang('Oops.. page is restricted.')
  @endslot

  @slot('message')
    @lang('We are sorry but you do not have permission to access this page')
  @endslot

  @slot('button')
    @button(['href' => route('dashboard')]) @lang('Home') @endbutton
  @endslot
@endfailure
