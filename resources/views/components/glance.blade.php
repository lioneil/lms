@card(['class' => 'mb-3'])
  @slot('body')
    @flex
      @layout(['attr' => 'row wrap justify-space-between align-center'])
        <div>
          @isset($text)
            <h3 class="grey--text text-uppercase">
              {{ $text }}
            </h3>
          @endisset
          @isset($count)
            <h1 class="display-2">{{ $count }}</h1>
          @endisset
        </div>
        <div>
          @isset($icon)
            @icon($icon, ['class' => 'grey--text', 'attr' => 'x-large'])
          @endisset
        </div>
      @endlayout
    @endflex
  @endslot
@endcard
