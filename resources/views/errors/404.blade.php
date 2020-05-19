@extends("website.components.page")

@section("title", "not found | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div class="card">
    <div class="card-body">
      <div class="container">
        <h1>Page not found</h1>
        <span>Unfortunately, we couldn't find this page for you.<br>
          Perhaps you want to return <a href="{{ route("home") }}">home</a>?</span>
      </div>
    </div>
  </div>
</div>
@endsection
