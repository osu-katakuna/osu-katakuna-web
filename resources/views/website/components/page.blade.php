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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset("/static/theme/theme.css") }}">
  @show

  @section("early-script")
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  @show
</head>

<body>
  @include("website.components.header")
  <div class="container">
    @yield("content")
  </div>
  @include("website.components.footer")
  <script src="//twemoji.maxcdn.com/2/twemoji.min.js?2.2"></script>
  @yield("script")
</body>
</html>
