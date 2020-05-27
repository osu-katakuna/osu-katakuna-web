@extends("admin.components.page")

@section("title", "Import replays")

@section("content")
<div class="card">
  <div class="card-body">
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
    <form action="{{ route("import-replays") }}" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="rp">Replays(select multiple files):</label>
        <input type="file" class="form-control-file" name="ReplayFile[]" id="rp" multiple="multiple">
      </div>
      <button type="submit" class="btn btn-primary">Import</button>
    </form>
  </div>
</div>
@endsection
