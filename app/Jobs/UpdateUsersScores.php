<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\UserStats;

class UpdateUsersScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $players;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->players = User::where('bot', '=', '0')->get();
    }

    private function updateScore($player, $gameMode = 0) {
        $st = new \App\UserStats;

        $st->user()->associate($player);
        $st->gameMode = $gameMode;
        $st->accuracy = $player->accuracy($gameMode);
        $st->score = $player->totalScore($gameMode);
        $st->pp = $player->pp($gameMode);
        $st->rank = $player->currentRankingPosition($gameMode);
        $st->play_count = $player->playCount($gameMode);

        $st->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->players as $player) {
          for($gamemode = 0; $gamemode < 4; $gamemode++) {
            if(!$player->currentStats($gamemode)) {
              $this->updateScore($player, $gamemode);
              continue;
            }
            $cS = $player->currentStats($gamemode);

            // if any stats change
            if(
              $cS->accuracy != $player->accuracy($gamemode) ||
              $cS->score != $player->totalScore($gamemode) ||
              $cS->pp != $player->pp($gamemode) ||
              $cS->rank != $player->currentRankingPosition($gamemode) ||
              $cS->play_count != $player->playCount($gamemode)
            ) {
              $this->updateScore($player, $gamemode); // update the score
            }
          }
        }
    }
}
