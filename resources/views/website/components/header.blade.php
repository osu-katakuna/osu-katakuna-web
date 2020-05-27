<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/">osu!katakuna</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

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
      @auth
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <div class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img src="{{ env("AVATAR_SERVER") }}/{{ Auth::user()->id }}" class="img-responsive img-rounded" style="max-height: 1.5em; max-width: 1.5em; border-radius: 500rem;">&nbsp;
              {{ Auth::user()->username }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="/u/{{ Auth::user()->id }}">Profile</a>
              <a class="dropdown-item" href="/logout">Log out</a>
            </div>
          </div>
        </li>
        @if (Auth::user()->hasPermission("admin.dashboard"))
          <li class="nav-item @if(Route::is('admin')) active @endif">
            <a class="nav-link" href="{{ route("admin") }}">Administration&nbsp;
              @if(Route::is('admin'))
              <span class="sr-only">(current)</span>
              @endif
            </a>
          </li>
        @endif
      </ul>
      @else
      <ul class="navbar-nav ml-auto">
        <li class="nav-item @if(Route::is('login')) active @endif">
          <a class="nav-link" href="{{ route("login") }}">Login
            @if(Route::is('login'))
            <span class="sr-only">(current)</span>
            @endif
          </a>
        </li>
        <li class="nav-item @if(Route::is('register')) active @endif">
          <a class="nav-link" href="{{ route("register") }}">Register
            @if(Route::is('register'))
            <span class="sr-only">(current)</span>
            @endif
          </a>
        </li>
      </ul>
      @endauth
    </div>
  </div>
</nav>
