@extends("website.components.page")

@section("title", "leaderboard | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div class="card">
    <div class="card-body table-responsive">
      <div class="container">
        <h1>leaderboard</h1>
        <div id="app">
          <leaderboard/>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
