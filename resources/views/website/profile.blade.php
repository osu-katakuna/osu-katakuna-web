@extends("website.components.page")

@section("title", "$user->username | osu!katakuna")

@section("content")
<div class="jumbotron">
  <div id="app">
    <user-card :user_id="{{ $user->id }}" />
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
                  <td class="right aligned">#{{ $user->currentStats(0) ? $user->currentStats(0)->rank : 0 }}</td>
                </tr>
                <tr>
                  <td><b>Global rank(osu!mania)</b></td>
                  <td class="right aligned">#{{ $user->currentStats(3) ? $user->currentStats(3)->rank : 0 }}</td>
                </tr>
                <tr>
                  <td><b>Country rank for &nbsp;<div class="flag flag-ro"></div>&nbsp;Romania</b></td>
                  <td class="right aligned">WIP</td>
                </tr>
                <tr>
                  <td><b>PP(osu!standard)</b></td>
                  <td class="right aligned">{{ $user->currentStats() ? $user->currentStats()->pp : 0 }}</td>
                </tr>
                <tr>
                  <td><b>Total score(osu!standard)</b></td>
                  <td class="right aligned">{{ number_format($user->currentStats(0) ? $user->currentStats(0)->score : 0) }}</td>
                </tr>
                <tr>
                  <td><b>Total score(osu!mania)</b></td>
                  <td class="right aligned">{{ number_format($user->currentStats(3) ? $user->currentStats(3)->score : 0) }}</td>
                </tr>
                <tr>
                  <td><b>Plays(osu!standard)</b></td>
                  <td class="right aligned">{{ $user->currentStats(0) ? $user->currentStats(0)->play_count : 0 }}</td>
                </tr>
                <tr>
                  <td><b>Plays(osu!mania)</b></td>
                  <td class="right aligned">{{ $user->currentStats(3) ? $user->currentStats(3)->play_count : 0 }}</td>
                </tr>
                <tr>
                  <td><b>Total Play Time</b></td>
                  <td class="right aligned">WIP</td>
                </tr>
                <tr>
                  <td><b>Accuracy(osu!standard)</b></td>
                  <td class="right aligned">{{ $user->currentStats(0) ? $user->currentStats(0)->accuracy : 0 }}%</td>
                </tr>
                <tr>
                  <td><b>Accuracy(osu!mania)</b></td>
                  <td class="right aligned">{{ $user->currentStats(3) ? $user->currentStats(3)->accuracy : 0 }}%</td>
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
