<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield("title") | osu!katakuna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @section("style")
  <link rel="stylesheet" href="{{ asset("/css/app.css") . "?" . \Str::random(10) }}">
  @show
  @section("early-script")
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
  @show
  @yield("script")
  @section("post-script")
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="{{ asset('js/app.js') }}?{{ rand() }}" defer></script>
  @show
</head>

<body class="hold-transition sidebar-mini layout-footer-fixed">
  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      @include("admin.components.navbar")
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="{{ route("admin") }}" class="brand-link">
        <span class="brand-text font-weight-light">osu!katakuna</span>
      </a>
      @include("admin.components.sidebar")
    </aside>

    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>@yield("title")</h1>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              @yield("content")
            </div>
          </div>
        </div>
      </section>
    </div>

    <footer class="main-footer">
      <div class="float-right d-none d-sm-block">
        <b>Version</b>&nbsp;1.0
      </div>
      <strong>Copyright &copy; {{ date("Y") }} <a href="https://github.com/osu-katakuna">osu!katakuna</a>.</strong> Based on AdminLTE.
    </footer>

    <aside class="control-sidebar control-sidebar-dark">
    </aside>
  </div>
</body>

</html>
