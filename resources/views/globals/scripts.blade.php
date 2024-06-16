<script>
window.$app = {
  logo: '{{ settings()->logo() }}',
  locale: '{{ app()->getLocale() }}',
  fallback_locale: '{{ config('app.fallback_locale') }}',
  meta: {!! settings()->containsKey('app') !!},
  theme: {!! settings()->containsKey('theme') !!},
  language: {!! json_encode(config('language')) !!},
  settings: {
    max_upload_file_size: '{{ bytesToHuman($fileSize = file_upload_max_size()) }}',
    max_upload_file_size_bytes: '{{ $fileSize }}',
  },
}
</script>
