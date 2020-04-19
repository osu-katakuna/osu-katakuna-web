<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>osu!katakuna</title>

  <link rel="apple-touch-icon" sizes="57x57" href="/static/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/static/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/static/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/static/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/static/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/static/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/static/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/static/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/static/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/static/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/static/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/static/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/static/favicon-16x16.png">
  <link rel="manifest" href="/static/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/static/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

  <!-- Styles -->
  <style>
    html,
    body {
      background-color: #fff;
      color: #636b6f;
      font-family: 'Nunito', sans-serif;
      font-weight: 200;
      height: 100vh;
      margin: 0;
    }

    .full-height {
      height: 100vh;
    }

    .flex-center {
      align-items: center;
      display: flex;
      justify-content: center;
    }

    .position-ref {
      position: relative;
    }

    .top-right {
      position: absolute;
      right: 10px;
      top: 18px;
    }

    .content {
      text-align: center;
    }

    .title {
      font-size: 84px;
    }

    .links>a {
      color: #636b6f;
      padding: 0 25px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: .1rem;
      text-decoration: none;
      text-transform: uppercase;
    }

    .m-b-md {
      margin-bottom: 30px;
    }
  </style>
</head>

<body>
  <div class="flex-center position-ref full-height">
    <div class="content">
      <div class="title m-b-md">
        <a style="color: #009688;">osu!katakuna</a>
      </div>

      <div class="links">
        <a style="color: #03A9F4;">Server is operational!</a>
      </div>
      <br><br>
      <div class="links">
        <a href="https://talnaci-alexandru.ro">Developer's website</a>
        <a href="#">Download switcher</a>
        <a href="/register">Register</a>
      </div>
    </div>
  </div>
</body>

</html>
