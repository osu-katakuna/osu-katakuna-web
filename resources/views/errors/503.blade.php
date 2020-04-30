@extends("website.components.page")

@section("title", "maintenance | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div class="card">
    <div class="card-body">
      <div class="container">
        <h1>Website under maintenance!</h1>
        <span>{{ $exception->getMessage() }}</span>
      </div>
    </div>
  </div>
</div>
@endsection
