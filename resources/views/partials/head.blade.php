<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  @stack('before:meta')
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}">
  <meta name="viewport" content="minimum-scale=1, initial-scale=1, width=device-width, shrink-to-fit=no">
  <title>
    @section('head:title')
      {{-- {{ @page()->title() }} --}}
    @show
    @section('head:subtitle')
      {{-- {{ @page()->subtitle() }} --}}
    @show
  </title>
  <base href="{{ url('/') }}">
  <meta name="description" content="{{ __('@page()->description()') }}">
  <!-- CSRF Token -->
  @stack('tokens')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
  @show
  @stack('favicons')
    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ theme('dist/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ theme('dist/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ theme('dist/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ theme('dist/favicons/manifest.json') }}">
    <link rel="mask-icon" color="{{ theme()->detail('colors.primary') }}" href="{{ theme('dist/favicons/safari-pinned-tab.svg') }}">
    <meta name="theme-color" content="{{ theme()->detail('colors.primary') }}">

  @show
  @stack('seo')
    <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
    <!-- <link rel="canonical" href="{{ url('/') }}"> -->
  @show
  @stack('after:meta')
  @stack('fonts')
    {{-- Display the links specified in config/stylesheets.php --}}
    {{-- TODO: do something about loading fonts --}}
    {{-- {!! font_link_tags('stylesheets') !!} --}}
  @show
  @stack('before:css')
    <style id="critical-css">{!! theme()->inlined() !!}</style>
    <link rel="preload" href="{{ theme('dist/css/fonts.css') }}?v={{ theme()->version() }}" as="style" onload="this.rel='stylesheet'">
    <link rel="preload" href="{{ theme('dist/css/app.css') }}?v={{ theme()->version() }}" as="style" onload="this.rel='stylesheet'">
    <link rel="preload" href="{{ theme('dist/js/vendor.js') }}?v={{ theme()->version() }}" as="script">
    <link rel="preload" href="{{ theme('dist/js/app.js') }}?v={{ theme()->version() }}" as="script">
  @show
  <!-- css -->
  @stack('css')
    <noscript><link rel="stylesheet" href="{{ theme('dist/css/fonts.css') }}?v={{ theme()->version() }}"></noscript>
    <noscript><link rel="stylesheet" href="{{ theme('dist/css/app.css') }}?v={{ theme()->version() }}"></noscript>
  @show
  <!-- css -->
  @stack('after:css')
    {!! theme()->styleloader() !!}
  @show
</head>
<body>
  @stack('noscript')
    @include('theme::partials.noscript')
  @show
