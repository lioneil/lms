<a
  href="{{ $param ?? url()->previous() }}"
  class="muted--text t-d-n"
  >
  @icon('chevron-left', ['class' => 'muted--text t-d-n', 'attr' => 'small'])
  @lang('Back')
</a>
