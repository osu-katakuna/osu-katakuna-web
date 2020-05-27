@extends("admin.components.page")

@section("title", "Add beatmaps")

@section("content")
<div class="card">
  <div class="card-body">
    @if (file_exists("/katakuna/beatmap-calculator/index.js"))
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
    <form action="{{ route("add-beatmap") }}" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="bm">Beatmap archive(*.osz):</label>
        <input type="file" class="form-control-file" name="BeatmapFile" id="bm">
      </div>
      <button type="submit" class="btn btn-primary">Import</button>
    </form>
    @else
    <div class="alert alert-danger">
      Katakuna Beatmap Calculator is not installed! Cannot continue.
    </div>
    @endif
  </div>
</div>
@endsection
