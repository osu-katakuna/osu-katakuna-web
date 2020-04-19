@extends("website.components.page")

@section("title", "$user->username | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div class="card">
    <div class="card-body">
      <div class="container">
        <div class="row">
          <div class="col-sm-2">
            <img src="https://a.ppy.sh/{{$user->id}}" alt="avatar" class="rounded float-left">
          </div>
          <div class="col-sm">
            <div class="container">
              <h1 class="ui header">
                {{$user->username}}
              </h1>
              <div id="socialstatus" class="subtitle">
                <i class="status-dot{{$user->online() ? " online" : "" }}"></i>
                <span>{{$user->online() ? "Online" : "Offline" }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="container">
        <div class="row">
          <div class="col-sm">
            <b>{{$user->username}}</b> is an osu!katakuna player.
          </div>
          <div class="col-sm">
            <table class="table">
              <tbody>
                <tr>
                  <td><b>Global rank(osu!standard)</b></td>
                  <td class="right aligned">#{{ $user->currentRankingPosition(0) }}</td>
                </tr>
                <tr>
                  <td><b>Global rank(osu!mania)</b></td>
                  <td class="right aligned">#{{ $user->currentRankingPosition(3) }}</td>
                </tr>
                <tr>
                  <td><b>Country rank for &nbsp;<div class="flag flag-ro"></div>&nbsp;Romania</b></td>
                  <td class="right aligned">WIP</td>
                </tr>
                <tr>
                  <td><b>PP</b></td>
                  <td class="right aligned">WIP</td>
                </tr>
                <tr>
                  <td><b>Total score(osu!standard)</b></td>
                  <td class="right aligned">{{number_format($user->totalScore(0))}}</td>
                </tr>
                <tr>
                  <td><b>Total score(osu!mania)</b></td>
                  <td class="right aligned">{{number_format($user->totalScore(3))}}</td>
                </tr>
                <tr>
                  <td><b>Plays(osu!standard)</b></td>
                  <td class="right aligned">{{$user->playCount(0)}}</td>
                </tr>
                <tr>
                  <td><b>Plays(osu!mania)</b></td>
                  <td class="right aligned">{{$user->playCount(3)}}</td>
                </tr>
                <tr>
                  <td><b>Total Play Time</b></td>
                  <td class="right aligned">WIP</td>
                </tr>
                <tr>
                  <td><b>Accuracy(osu!standard)</b></td>
                  <td class="right aligned">{{$user->accuracy(0)}}%</td>
                </tr>
                <tr>
                  <td><b>Accuracy(osu!mania)</b></td>
                  <td class="right aligned">{{$user->accuracy(3)}}%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
