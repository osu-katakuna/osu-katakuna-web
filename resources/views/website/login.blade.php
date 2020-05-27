@extends("website.components.page")

@section("title", "login | osu!katakuna")

@section("content")
<header class="jumbotron">
  <div class="card">
    <div class="card-body">
      <h1>Login to osu!katakuna</h1>
      @if (session("redirect"))
        <div class="alert alert-info" role="alert">Please log in to continue.</div>
      @endif
      @if (isset($message))
        <div class="alert alert-info" role="alert">{{ $message }}</div>
      @endif
      @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      <form action="{{ route("login") }}" method="post">
        <div class="form-group">
          <label for="username">Username or E-mail address</label>
          <input type="text" class="form-control" name="username" value="{{ old('username') }}">
        </div>
        <div class="form-group">
          <label for="password">Password <a href="#">(forgot your password?)</a></label>
          <input type="password" class="form-control" name="password" value="{{ old('password') }}">
        </div>
        <div class="form-group form-check">
          <input type="checkbox" class="form-check-input" name="remember" id="remember">
          <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </div>
</header>
@endsection
