<div class="svg-responsive text-center text-{{ $context ?? 'primary' }} {{ $class ?? null }}">
  @includeIf($param, ['width' => $width ?? null, 'height' => $height ?? null])
</div>
