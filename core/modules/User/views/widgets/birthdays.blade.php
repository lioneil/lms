@card
  @slot('title')@lang('Birthdays this Month')@endslot
  @slot('body')
    @if ($celebrants->isEmpty())
      @svg('theme::icons.accept', ['context' => 'primary', 'height' => '200px', 'class' => 'mb-3'])
      <p class="text-center text-muted">@lang('No birthday celebrants this Month.')</p>
    @endif
    @foreach ($celebrants as $celebrant)
      <div class="mb-4">
        @avatar($celebrant->avatar)
        <a href="{{ route('users.profile', $celebrant->username) }}">{{ $celebrant->displayname }} - {{ $celebrant->detail('birthday') }}</a>
      </div>
    @endforeach
  @endslot
@endcard
