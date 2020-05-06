<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/">osu!katakuna</a>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav">
        <li class="nav-item @if(Route::is('home')) active @endif">
          <a class="nav-link" href="{{ route("home") }}">Home
            @if(Route::is('home'))
              <span class="sr-only">(current)</span>
            @endif
          </a>
        </li>
        <li class="nav-item @if(Route::is('leaderboard')) active @endif">
          <a class="nav-link" href="{{ route("leaderboard") }}">Leaderboard
            @if(Route::is('leaderboard'))
              <span class="sr-only">(current)</span>
            @endif
          </a>
        </li>
      </ul>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item @if(Route::is('register')) active @endif">
          <a class="nav-link" href="{{ route("register") }}">Register
            @if(Route::is('register'))
              <span class="sr-only">(current)</span>
            @endif
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
