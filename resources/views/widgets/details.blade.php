@card(['class' => 'mb-3'])
  @slot('body')
    {{ $details->get('app:title') }} - {{ $details->get('app:tagline') }}
  @endslot
@endcard
