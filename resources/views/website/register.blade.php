@extends("website.components.page")

@section("title", "Register | osu!katakuna")

@section("content")
<header class="jumbotron">
  <div class="card">
    <div class="card-body">
      <h1>osu!katakuna registration form</h1>
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
      <form action="/register" method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" name="username" value="{{ old('username') }}">
        </div>
        <div class="form-group">
          <label for="email">E-mail address</label>
          <input type="email" class="form-control" name="email" value="{{ old('email') }}">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="password" value="{{ old('password') }}">
        </div>
        <button type="submit" class="btn btn-primary">Create account</button>
      </form>
    </div>
  </div>
</header>
@endsection
