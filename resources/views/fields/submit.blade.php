<button
  class="btn {{ $class ?? 'btn-primary' }}"
  type="submit"
  {{ $attr ?? null }}
  {{ ($disabled ?? false) ? 'disabled' : null  }}
>
  {{ $text ?? $label ?? __('Save') }}
</button>
