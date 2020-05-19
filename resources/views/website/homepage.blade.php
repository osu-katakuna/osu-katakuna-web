@extends("website.components.page")

@section("title", "home | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div class="card">
    <div class="card-body">
      <div class="container">
        <img id="logo" class="img-fluid rounded mx-auto d-block" src="/static/logo-preview.png">
      </div>
    </div>
  </div>
  <br>
  @auth
  <div class="card">
    <div class="card-body">
      <div class="container">
        <h3>Welcome back, {{ Auth::user()->username }}!</h3>
        <span>Welcome back on osu!katakuna! There are {{ count(\App\User::where("banned", "=", "0")->get()) }} registered user(s) and {{ count(\App\OsuUserSession::all()) }} online user(s). It's a beautiful day to play some maps, isn't it?</span>
      </div>
    </div>
  </div>
  @endauth
  <div class="card">
    <div class="card-body">
      <div class="container">
        <h3>Welcome to osu!katakuna</h3>
        <span>osu!katakuna is the first osu! private server launched in Romania that has been written from scratch. It offers Multiplayer, PP(still in the works), a very friendly community and a development team that makes osu!katakuna stable and fun to play.</span><br>
        <br>
        <span>osu!katakuna is the result of the hard work of <a href="https://github.com/talnacialex">talnacialex</a>, that wanted to create his first private server for a game.</span><br>
        <br>
        <span>The best part about our server is the fact that we are <a href="https://github.com/osu-katakuna">open-source</a>! Yeah, you heard us right, OPEN-SOURCE! So that everyone could propose or add new features to our server.</span><br>
        <span>We also have a discord server, so if you want to speak to us, then feel free to join <a href="https://discord.gg/QDP4muY">here</a>!</span><br>
        <br><h3>Join our server! You won't regret it! 😀</h3><br>
      </div>
    </div>
  </div>
</div>
@endsection
