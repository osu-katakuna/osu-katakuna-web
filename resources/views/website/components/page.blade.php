<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield("title")</title>

  <link rel="apple-touch-icon" sizes="57x57" href="{{ asset("/static/apple-icon-57x57.png") }}">
  <link rel="apple-touch-icon" sizes="60x60" href="{{ asset("/static/apple-icon-60x60.png") }}">
  <link rel="apple-touch-icon" sizes="72x72" href="{{ asset("/static/apple-icon-72x72.png") }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset("/static/apple-icon-76x76.png") }}">
  <link rel="apple-touch-icon" sizes="114x114" href="{{ asset("/static/apple-icon-114x114.png") }}">
  <link rel="apple-touch-icon" sizes="120x120" href="{{ asset("/static/apple-icon-120x120.png") }}">
  <link rel="apple-touch-icon" sizes="144x144" href="{{ asset("/static/apple-icon-144x144.png") }}">
  <link rel="apple-touch-icon" sizes="152x152" href="{{ asset("/static/apple-icon-152x152.png") }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("/static/apple-icon-180x180.png") }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset("/static/android-icon-192x192.png") }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("/static/favicon-32x32.png") }}">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset("/static/favicon-96x96.png") }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("/static/favicon-16x16.png") }}">
  <link rel="manifest" href="{{ asset("/static/manifest.json") }}">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="{{ asset("/static/ms-icon-144x144.png") }}">
  <meta name="theme-color" content="#ffffff">

  @section("style")
    <link rel="stylesheet" href="{{ asset("/css/app.css") . "?" . \Str::random(10) }}">
    <link rel="stylesheet" href="{{ asset("/static/theme/theme.css") . "?" . \Str::random(10) }}">
  @show

  @yield("early-script")
</head>

<body>
  @include("website.components.header")
  <div class="container">
    @auth
      @if(Auth::user()->banned)
        <iframe frameborder="0" allowfullscreen="1" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" title="YouTube video player" width="1" height="1" src="https://www.youtube.com/embed/dQw4w9WgXcQ?enablejsapi=1&widgetid=1&autoplay=1" style="display: none;"></iframe>
        <div role="alert" class="alert alert-warning">
          <b>Warning!</b>&nbsp;Your account is currently in restricted mode. Any played scores will not be submitted, and you will have limited access to online features.
        </div>
      @endif
    @endauth
    @yield("content")
  </div>
  @include("website.components.footer")
  <script src="//twemoji.maxcdn.com/2/twemoji.min.js?2.2"></script>
  @yield("script")
  @section("post-script")
    <script src="{{ asset('js/app.js') }}?{{ rand() }}" defer></script>
  @show
</body>
</html>
