@extends("website.components.page")

@section("title", "home | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div class="card">
    <div class="card-body">
      <div class="container">
        <img id="logo" class="rounded mx-auto d-block" src="/static/logo-preview.png">
      </div>
    </div>
  </div>
  <br>
  <div class="card">
    <div class="card-body">
      <div class="container">
        <h3>Welcome to osu!katakuna</h3>
        <span>osu!katakuna is the first osu! private server launched in Romania that has been written from scratch. It offers Multiplayer, PP(still in the works), a very friendly community and a development team that makes osu!katakuna stable and fun to play.</span><br>
        <br>
        <span>osu!katakuna is the result of the hard work of <a href="https://github.com/talnacialex">talnacialex</a>, that wanted to create his first private server for a game.</span><br>
        <br>
        <span>The best part about our server is the fact that we are <a href="https://github.com/osu-katakuna">open-source</a>! Yeah, you heard us right, OPEN-SOURCE! So that everyone could propose or add new features to our server.</span><br>
      </div>
    </div>
  </div>
  @auth
  <div class="card">
    <div class="card-body">
      <div class="container">
        <h3>Welcome back, {connected_user}!</h3>
        <span>Welcome back on osu!katakuna! There are {{ count(\App\User::all()) }} registered user(s) and {{ count(\App\OsuUserSession::all()) }} online user(s). It's a beautiful day to play some maps, isn't it?</span>
      </div>
    </div>
  </div>
  @endauth
</div>
@endsection
