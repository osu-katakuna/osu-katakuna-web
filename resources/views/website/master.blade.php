{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    osu!katakuna
  </title>
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
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset("/osu/css/app.css") . "?" . \Str::random(10) }}">
  @show
</head>

<body class="
            osu-layout
            osu-layout--body
            t-section
        ">
  <style>
    :root {
      --base-hue: 333;
      --base-hue-deg: 333deg;
    }
  </style>
  <div id="overlay" class="blackout blackout--overlay" style="display: none;"></div>
  <div class="blackout js-blackout" data-visibility="hidden"></div>

  <div class="osu-layout__section osu-layout__section--full js-content">
    @yield('content')
  </div>
</body>

</html>
