@extends("website.components.page")

@section("title", "spectate $user->username | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div id="app">
    <spectator :user_id="{{ $user->id }}" />
  </div>
</div>
@endsection
