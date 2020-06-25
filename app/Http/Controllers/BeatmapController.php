<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Beatmap;
use App\BeatmapSet;
use App\User;
use App\UserPlayBeatmap;
use App\Osu\Chart;
use App\OsuConsts;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Http;

class BeatmapController extends Controller
{
  public static function DownloadBeatmap($query, $set = false, $direct_import = false, $bm = false) {
    if(!$direct_import) {
      $maps = json_decode(file_get_contents("http://bloodcat.com/osu/?mod=json&c=" . ($set ? "s" : ($bm ? "b" : "o")) . "&s=0,1,2,3&m=0,1,2,3&q=" . urlencode($query) . "&p=1"));
      if(count($maps) < 1) return false;
    } else {
      $maps = array($query);
    }

    foreach($maps as $map) {
      $id = $map->id;
      $artist = $map->artist;
      $title = $map->title;
      $creator = $map->creator;

      $beatmap = new Beatmap();
      if($map->id != -1) $beatmap->id = $map->id;
      $beatmap->sync_date = $map->synced;
      $beatmap->title = $map->title;
      $beatmap->title_unicode = $map->titleU;
      $beatmap->artist = $map->artist;
      $beatmap->artist_unicode = $map->artistU;
      $beatmap->status = $map->status;
      $beatmap->creator_id = $map->creatorId;
      $beatmap->creator = $map->creator;
      $beatmap->tags = $map->tags;
      $beatmap->source = $map->source;
      $beatmap->genre_id = $map->genreId;
      $beatmap->filename = BeatmapController::FileNameClean("beatmaps/$artist - $title ($creator).osz");

      if(!Beatmap::find($map->id)) {
        $beatmap->save();
      } else {
        $beatmap = Beatmap::find($map->id);
      }

      if(!file_exists(storage_path() . BeatmapController::FileNameClean("/app/beatmaps/$artist - $title ($creator).osz")) && !$direct_import) {
        Storage::put(BeatmapController::FileNameClean("beatmaps/$artist - $title ($creator).osz"), file_get_contents("http://bloodcat.com/osu/s/$id"));
      }

      BeatmapController::AddBeatmapToDB(storage_path() . BeatmapController::FileNameClean("/app/beatmaps/$artist - $title ($creator).osz"), $beatmap, $map->beatmaps);
    }

    return true;
  }

  public static function GetOsuBMMetadata($data, $name) {
    return explode("\r\n", explode("$name:", $data)[1])[0];
  }

    public static function AddBeatmapToDB($beatmap_path, $_beatmap, $_beatmaps) {
      $beatmap = new \ZipArchive();

      $beatmap->open($beatmap_path);
      for ($i = 0; $i < $beatmap->numFiles; $i++) {
          $file = $beatmap->statIndex($i);
          if(substr($file['name'], strlen($file['name']) - 4, 4) == ".osu") {
            $map_data = NULL;
            $diff = BeatmapController::GetOsuBMMetadata($beatmap->getFromIndex($i), "Version");

            foreach($_beatmaps as $b) {
              if($b->name == $diff) {
                $map_data = $b;
                break;
              }
            }

            if($map_data == NULL) continue;

            if(!BeatmapSet::find($map_data->id)) {
              $m = new BeatmapSet();
              $m->id = $map_data->id;
              $m->name = $map_data->name;
              $m->mode = $map_data->mode;
              $m->hp = $map_data->hp;
              $m->cs = $map_data->cs;
              $m->od = $map_data->od;
              $m->ar = $map_data->ar;
              $m->bpm = $map_data->bpm;
              $m->length = $map_data->length;
              $m->stars = $map_data->star;
              $m->md5 = $map_data->hash_md5;
              $m->status = $map_data->status;
              $m->author = $map_data->author;
              $m->filename = $file['name'];
              if(array_key_exists("aim", $map_data)) {
                $m->aim = $map_data->aim;
              }
              if(array_key_exists("speed", $map_data)) {
                $m->speed = $map_data->speed;
              }

              $m->beatmap()->associate($_beatmap);
              $m->save();
            }

            Storage::put(BeatmapController::FileNameClean("beatmaps/" . $file['name']),  $beatmap->getFromIndex($i));
          }
      }
    }

    public static function FileNameClean($filename) {
      return str_replace(":", "", $filename);
    }

    function StreamMap(Request $req, $map) {
      $bm = BeatmapSet::where("filename", "=", $map)->get()->first();

      if($bm) {
        return file_get_contents(storage_path() . "/app/beatmaps/" . $bm->filename);
      } else {
        BeatmapController::DownloadBeatmap(explode(" [", $map)[0]);
        $bm = BeatmapSet::where("filename", "=", $map)->get()->first();

        if($bm) {
          return file_get_contents(storage_path() . "/app/beatmaps/" . $bm->filename);
        }
      }
    }

    function search(Request $req) {
      $r = $req->get("r");
      $query = $req->get("q");

      $maps = Beatmap::where("title", "LIKE", "%" . $query . "%")
        ->orWhere("artist", "LIKE", "%" . $query . "%")
        ->orWhere("creator", "LIKE", "%" . $query . "%")
        ->orWhere("tags", "LIKE", "%" . $query . "%");

      if($query == "Newest") {
        $maps = Beatmap::orderBy("sync_date", "DESC");
      }

      if($r == 0 || $r == 7) {
        $maps = $maps->orWhere("status", "=", "1");
      } else if($r == 2) {
        $maps = $maps->orWhere("status", "=", "2");
      } else if($r == 3) {
        $maps = $maps->orWhere("status", "=", "3");
      } else if($r == 5) {
        $maps = $maps->orWhere("status", "=", "0");
      }

      $mode = $_GET["m"];
      // TODO: implement modes

      $page = $req->get("p");
      // TODO: implement pages

      $maps = $maps->skip($page * 40)->take(40)->get();

      if(count($maps) >= 40) {
        echo 101;
      } else {
        echo count($maps);
      }
      echo "\r\n";

      foreach($maps as $map) {
        if(count($map->sets) < 1) continue;
        $diffs = "";
        foreach ($map->sets as $diff) {
          $diff->stars = round($diff->stars, 2);
    			$diffs .= "$diff->name ★$diff->stars@$diff->mode,";
    		}
    		$diffs = rtrim($diffs, ',');
        echo "$map->id.osz|$map->artist|$map->title|$map->creator|$map->status|10.00000|$map->sync_date|$map->id|" . $map->sets->first()->id . "|0|0|0||$diffs|\r\n";
      }
    }

    function set_search(Request $req) {
      if (isset($_GET['b']) && !empty($_GET['b'])) {
        $maps = json_decode(file_get_contents("http://bloodcat.com/osu/?mod=json&c=b&q=" . $req->get("b")));
        if(count($maps) >= 40) {
          echo 101;
        } else {
          echo count($maps);
        }
        echo "\r\n";

        foreach($maps as $map) {
          $diffs = "";
          foreach ($map->beatmaps as $diff) {
            $diff->star = round($diff->star, 2);
      			$diffs .= "$diff->name ★$diff->star@$diff->mode,";
      		}
      		$diffs = rtrim($diffs, ',');
          echo "$map->id.osz|$map->artist|$map->title|$map->creator|$map->status|10.00000|$map->synced|$map->id|" . $map->beatmaps[0]->id . "|0|0|0||$diffs|\r\n";
        }
    	}
    }

    public static function StreamBeatmapFromStorage($f) {
      $path = storage_path() . "/app/" . $f;
      if(!file_exists($path)) throw new \Exception("File $path not found!");

      return response()->file($path);
    }

    function download(Request $req, $id) {
        $b = Beatmap::find($id);
        if($b) {
          if(!file_exists(storage_path() . "/app/" . $b->filename)) {
            BeatmapController::DownloadBeatmap($id, true);
            $b = Beatmap::find($id);
            if($b) {
              return BeatmapController::StreamBeatmapFromStorage($b->filename);
            } else {
              return "";
            }
          }
          return BeatmapController::StreamBeatmapFromStorage($b->filename);
        } else {
          BeatmapController::DownloadBeatmap($id, true);
          $b = Beatmap::find($id);
          if($b) {
            return BeatmapController::StreamBeatmapFromStorage($b->filename);
          } else {
            return "";
          }
        }
    }

    function thumbnail(Request $req, $id) {
      return file_get_contents("https://b.ppy.sh/thumb/$id.jpg");
      $b = Beatmap::find($id);
      if($b) {
        return file_get_contents("https://b.ppy.sh/thumb/$id.jpg");
      } else {
        return "Map not found!";
      }
    }

    function thumbnail_large(Request $req, $id) {
      return file_get_contents("https://b.ppy.sh/thumb/$id" . "l.jpg");
      $b = Beatmap::find($id);
      if($b) {
        return file_get_contents("https://b.ppy.sh/thumb/$id" . "l.jpg");
      } else {
        return "Map not found!";
      }
    }

    function song_preview(Request $req, $id) {
      return file_get_contents("https://b.ppy.sh/preview/$id.mp3");
      $b = Beatmap::find($id);
      if($b) {
        return file_get_contents("https://b.ppy.sh/preview/$id.mp3");
      } else {
        return "Map not found!";
      }
    }

    function gameModeToString($gameMode) {
      if($gameMode == 0) return "standard";
      if($gameMode == 1) return "taiko";
      if($gameMode == 2) return "ctb";
      if($gameMode == 3) return "mania";
      if($gameMode == 4) return "standard [RELAX]";
    }

    function submit_score(Request $req) {
      if (!isset($_POST['score']) || !isset($_POST['iv']) || !isset($_POST['pass']) || empty($_POST['score']) || empty($_POST['iv']) || empty($_POST['pass'])) {
    		return "error: beatmap";
    	}

      if (isset($_POST['osuver']) && !empty($_POST['osuver'])) {
    		$key = sprintf('osu!-scoreburgr---------%s', $_POST['osuver']);
    	} else {
    		$key = 'h89f2-890h2h89b34g-h80g134n90133'; // fixed key
    	}

      $scoreData = @mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($_POST['score']), MCRYPT_MODE_CBC, base64_decode($_POST['iv']));
      $replay_file = Str::random(40) .".osr";
      $req->file('score')->storeAs('replays', $replay_file);

    	$scoreDataArray = explode(':', $scoreData);
    	$username = rtrim($scoreDataArray[1], ' ');
      $user = User::where([["username", "=", $username], ["password_hash", "=", hash("sha256", $_POST["pass"])]])->get()->first();
      $bm = BeatmapSet::where("md5", "=", $scoreDataArray[0])->get()->first();

      if(!$bm) {
        return "error: beatmap";
      }

      if(!$user) {
        return "error: pass";
      }

      $fileChecksum = $scoreDataArray[0];
      $count300 = $scoreDataArray[3];
      $count100 = $scoreDataArray[4];
      $count50 = $scoreDataArray[5];
      $countGeki = $scoreDataArray[6];
      $countKatu = $scoreDataArray[7];
      $miss = $scoreDataArray[8];
      $score = $scoreDataArray[9];
      $maxCombo = $scoreDataArray[10];
      $fc = $scoreDataArray[11] == 'True';
      $archivedLetter = $scoreDataArray[12];
      $mods = $scoreDataArray[13];
      $pass = $scoreDataArray[14] == 'True';
      $gameMode = ($mods & OsuConsts::Relax) ? 4 : $scoreDataArray[15];
      $time = date("ymdHms");
      $version = $req->get("osuver");
      $clientHash = "thismustbeusersclienthashfromdb";

      $ch = "chickenmcnuggets" . ($count100 + $count300) . "o15" . $count50 . $countGeki . "smustard" . $countKatu . $miss . "uu" . $fileChecksum . $maxCombo . ($fc ? "True" : "False") . $username . $score . $archivedLetter . $mods . $gameMode . "Q" . $time . $version . $clientHash;
      //dd($time, $version, $clientHash, $ch, $scoreDataArray);

      $beatmap_ranking = new Chart();
      $beatmap_ranking->id = "beatmap";
      $beatmap_ranking->name = "Beatmap Ranking";
      $beatmap_ranking->username = $user->username;
      $beatmap_ranking->url = "https://osu.ppy.sh/b/" . $bm->beatmap->id;
      $beatmap_ranking->rankBefore = $bm->positionOfUser($user);

      $last_good_play = $bm->plays()->where("user_id", "=", $user->id)->orderBy("maxCombo", "DESC")->get()->first();

      if($last_good_play) {
        $beatmap_ranking->maxComboBefore = $last_good_play->maxCombo;
        $beatmap_ranking->accuracyBefore = $last_good_play->accuracy() * 100;
        $beatmap_ranking->rankedScoreBefore = $last_good_play->score;
        $beatmap_ranking->totalScoreBefore = $last_good_play->score;
        $beatmap_ranking->ppBefore = $last_good_play->pp();
      }

      $overall_ranking = new Chart();
      $overall_ranking->id = "overall";
      $overall_ranking->name = "Global Ranking";
      $overall_ranking->username = $user->username;
      $overall_ranking->url = "https://osu.ppy.sh/u/" . $user->id;
      $overall_ranking->rankBefore = $user->currentRankingPosition($gameMode);
      $overall_ranking->accuracyBefore = $user->accuracy($gameMode);
      $overall_ranking->rankedScoreBefore = $user->totalScore($gameMode);
      $overall_ranking->totalScoreBefore = $user->totalScore($gameMode);
      $overall_ranking->ppBefore = $user->pp($gameMode);

      // this is how you do an achievement :D

      //$achv = [
      //    "Icon" => "osu-combo-2000",
      //    "DisplayName" => "Debugging",
      //    "Description" => "If you see this, then the scoring system is debugged right now! :D"
      //];

      // $overall_ranking->addAchievement($achv);

      $play = new UserPlayBeatmap();
      $play->player()->associate($user);
      $play->beatmap_set()->associate($bm);
      $play->count300 = $count300;
      $play->count100 = $count100;
      $play->count50 = $count50;
      $play->countGeki = $countGeki;
      $play->countKatu = $countKatu;
      $play->miss = $miss;
      $play->score = $score;
      $play->maxCombo = $maxCombo;
      $play->fc = $fc;
      $play->archivedLetter = $archivedLetter;
      $play->mods = $mods;
      $play->pass = $pass;
      $play->gameMode = $gameMode;
      $play->state = isset($_POST["x"]) ? $_POST["x"] : 2;
      $play->replay_file = $replay_file;
      $play->save();

      $overall_ranking->rankAfter = $user->currentRankingPosition($gameMode);
      $overall_ranking->accuracyAfter = $user->accuracy($gameMode);
      $overall_ranking->rankedScoreAfter = $user->totalScore($gameMode);
      $overall_ranking->totalScoreAfter = $user->totalScore($gameMode);
      $overall_ranking->onlineScoreId = $play->id;
      $overall_ranking->ppAfter = $user->pp($gameMode);

      $beatmap_ranking->onlineScoreId = $play->id;
      $beatmap_ranking->rankAfter = $bm->positionOfUser($user);
      $beatmap_ranking->totalScoreAfter = $play->score;
      $beatmap_ranking->rankedScoreAfter = $play->score;
      $beatmap_ranking->accuracyAfter = $play->accuracy() * 100;
      if($beatmap_ranking->accuracyAfter - $beatmap_ranking->accuracyBefore == 0) {
        $beatmap_ranking->accuracyBefore = 0;
      }
      $beatmap_ranking->maxComboAfter = $play->maxCombo;
      $beatmap_ranking->ppAfter = $play->pp();

      $mods_text = "";
      if($mods != "0") {
        $mods_text = " +";
        $mods_text .= ($mods & OsuConsts::Relax2) ? "AT" : "";
        $mods_text .= ($mods & OsuConsts::HardRock) ? "HR" : "";
        $mods_text .= ($mods & OsuConsts::NoFail) ? "NF" : "";
        $mods_text .= ($mods & OsuConsts::SuddenDeath) ? "SD" : "";
        $mods_text .= ($mods & OsuConsts::Perfect) ? "PF" : "";
        $mods_text .= ($mods & OsuConsts::DoubleTime) ? "DT" : "";
        $mods_text .= ($mods & OsuConsts::Nightcore) ? "NC" : "";
        $mods_text .= ($mods & OsuConsts::Hidden) ? "HD" : "";
        $mods_text .= ($mods & OsuConsts::Flashlight) ? "FL" : "";
        $mods_text .= ($mods & OsuConsts::Relax) ? "RX" : "";
        $mods_text .= ($mods & OsuConsts::Autoplay) ? "AP" : "";
        $mods_text .= ($mods & OsuConsts::SpunOut) ? "SO" : "";
        $mods_text .= ($mods & OsuConsts::Key1) ? "1K" : "";
        $mods_text .= ($mods & OsuConsts::Key2) ? "2K" : "";
        $mods_text .= ($mods & OsuConsts::Key3) ? "3K" : "";
        $mods_text .= ($mods & OsuConsts::Key4) ? "4K" : "";
        $mods_text .= ($mods & OsuConsts::Key5) ? "5K" : "";
        $mods_text .= ($mods & OsuConsts::Key6) ? "6K" : "";
        $mods_text .= ($mods & OsuConsts::Key7) ? "7K" : "";
        $mods_text .= ($mods & OsuConsts::Key8) ? "8K" : "";
        $mods_text .= ($mods & OsuConsts::Key9) ? "9K" : "";
      }

      if($beatmap_ranking->rankAfter == 1 && $pass && !$user->banned) {
        $data = "Action:SendMessageToChannel\n";
        $data .= "Channel:#announce\n";
        $data .= "User:KatakunaBot\n\n";
        $data .= $user->username . " has placed #1 on " . $bm->beatmap->artist . " - " . $bm->beatmap->title . "(" . $bm->beatmap->creator . ") [" . $bm->name . "]" . $mods_text . ".\n";

        file_put_contents("/var/spool/katakuna/" . Str::random(10), $data);

        if(env("DISCORD_WEBHOOK_SCORES") !== NULL) {
          Http::post(env("DISCORD_WEBHOOK_SCORES"), [
              'embeds' => [
                [
                  "description" => $user->username . " has placed #1 on " . $bm->beatmap->artist . " - " . $bm->beatmap->title . "(" . $bm->beatmap->creator . ") [" . $bm->name . "]" . $mods_text . " in **osu!" . BeatmapController::gameModeToString($gameMode) . "** mode",
                  "color" => 8513040,
                  "footer" => [
                      "text" => "submitted on katakuna!kodachi"
                  ],
                  "author" => [
                      "name" => $user->username,
                      "url" => route("user", ["id" => $user->id]),
                      "icon_url" => env("AVATAR_SERVER") . "/" . $user->id
                  ],
                  "image" => [
                      "url" => "https://a.sayobot.cn/beatmaps/" . $bm->beatmap->id . "/covers/cover.jpg"
                  ],
                  "fields" => [
                    [
                      "name" => "x300",
                      "value" => $play->count300,
                      "inline" => true
                    ],
                    [
                      "name" => "x100",
                      "value" => $play->count100,
                      "inline" => true
                    ],
                    [
                      "name" => "x50",
                      "value" => $play->count50,
                      "inline" => true
                    ],
                    [
                      "name" => "Geki",
                      "value" => $play->countGeki,
                      "inline" => true
                    ],
                    [
                      "name" => "Katu",
                      "value" => $play->countKatu,
                      "inline" => true
                    ],
                    [
                      "name" => "Score",
                      "value" => $play->score,
                      "inline" => true
                    ],
                    [
                      "name" => "Misses",
                      "value" => $play->miss,
                      "inline" => true
                    ],
                    [
                      "name" => "Gained PP",
                      "value" => $beatmap_ranking->ppAfter,
                      "inline" => true
                    ]
                  ]
                ]
              ]
          ]);
        }
      }

      if($user->banned) {
        if(env("DISCORD_WEBHOOK_AC") !== NULL) {
          Http::post(env("DISCORD_WEBHOOK_AC"), [
              'embeds' => [
                [
                  "description" => "A banned user tried to submit a score. This play is automatically deleted. Beatmap: " . $bm->beatmap->artist . " - " . $bm->beatmap->title . "(" . $bm->beatmap->creator . ") [" . $bm->name . "]" . $mods_text . " in **osu!" . BeatmapController::gameModeToString($gameMode) . "** mode",
                  "color" => 13632027,
                  "footer" => [
                      "text" => "katakuna!kodachi internal anti cheat"
                  ],
                  "author" => [
                      "name" => $user->username . "[BANNED]",
                      "url" => route("user", ["id" => $user->id]),
                      "icon_url" => env("AVATAR_SERVER") . "/" . $user->id
                  ],
                  "image" => [
                      "url" => "https://a.sayobot.cn/beatmaps/" . $bm->beatmap->id . "/covers/cover.jpg"
                  ],
                  "fields" => [
                    [
                      "name" => "x300",
                      "value" => $play->count300,
                      "inline" => true
                    ],
                    [
                      "name" => "x100",
                      "value" => $play->count100,
                      "inline" => true
                    ],
                    [
                      "name" => "x50",
                      "value" => $play->count50,
                      "inline" => true
                    ],
                    [
                      "name" => "Geki",
                      "value" => $play->countGeki,
                      "inline" => true
                    ],
                    [
                      "name" => "Katu",
                      "value" => $play->countKatu,
                      "inline" => true
                    ],
                    [
                      "name" => "Score",
                      "value" => $play->score,
                      "inline" => true
                    ],
                    [
                      "name" => "Misses",
                      "value" => $play->miss,
                      "inline" => true
                    ],
                    [
                      "name" => "PP",
                      "value" => $beatmap_ranking->ppAfter,
                      "inline" => true
                    ]
                  ]
                ]
              ]
          ]);
        }
        $play->delete();
      }

      // \Artisan::call('schedule:run');
      $st = new \App\UserStats;

      $st->user()->associate($user);
      $st->gameMode = $gameMode;
      $st->accuracy = $user->accuracy($gameMode);
      $st->score = $user->totalScore($gameMode);
      $st->pp = $user->pp($gameMode);
      $st->rank = $user->currentRankingPosition($gameMode);
      $st->play_count = $user->playCount($gameMode);

      $st->save();

      return "beatmapId:" .
        $bm->beatmap->id .
        "|beatmapSetId:" .
        $bm->id .
        "|beatmapPlaycount:" .
        count($bm->plays) .
        "|beatmapPasscount:" .
        count($bm->plays()->where("pass", "=", "1")->get()) .
        "|approvedDate:" .
        ($bm->beatmap->created_at != NULL ? $bm->beatmap->created_at : "") .
        "\n\n" .
        $beatmap_ranking->ToString().
        "\n" .
        $overall_ranking->ToString();
    }

    public static function UploadedBeatmapRegister($path) {
      $map_data = array();
      $map_data["beatmaps"] = array();
      $_beatmap = new \ZipArchive();

      $_beatmap->open($path);
      for ($i = 0; $i < $_beatmap->numFiles; $i++) {
          $file = $_beatmap->statIndex($i);
          if(substr($file['name'], strlen($file['name']) - 4, 4) == ".osu") {
              Storage::put(BeatmapController::FileNameClean("beatmaps/" . $file['name']),  $_beatmap->getFromIndex($i));
              $process = new Process(["node", "/katakuna/beatmap-calculator", BeatmapController::FileNameClean(storage_path() . "/app/beatmaps/" . $file['name'])]);
              $process->run();
              $output = $process->getOutput();
              if(!$process->isSuccessful()) {
                return redirect('/add-beatmap')
                            ->withErrors(["Failed to import " . BeatmapController::FileNameClean(storage_path() . "/app/beatmaps/" . $file['name'])]);
              } else {
                $map_set = (object) json_decode(join("", explode("\n", $output)));
                array_push($map_data["beatmaps"], $map_set);
                $map_data["synced"] = date("Y-m-d H:m:s");
                $map_data["status"] = 2;
                $map_data["title"] = $map_set->title;
                $map_data["titleU"] = "";
                $map_data["artist"] = $map_set->artist;
                $map_data["artistU"] = "";
                $map_data["creatorId"] = NULL;
                $map_data["creator"] = $map_set->creator;
                $map_data["rankedAt"] = null;
                $map_data["tags"] = "";
                $map_data["source"] = "";
                $map_data["genreId"] = "1";
                $map_data["id"] = BeatmapController::GetOsuBMMetadata($_beatmap->getFromIndex($i), "BeatmapSetID") ? BeatmapController::GetOsuBMMetadata($_beatmap->getFromIndex($i), "BeatmapSetID") : rand(); // addressing an issue when Set ID is not defined. Giving a random ID
                $map_data["aim"] = $map_set->aim;
                $map_data["speed"] = $map_set->speed;
              }
          }
      }

      Storage::put(BeatmapController::FileNameClean("beatmaps/" . $map_data["artist"] . " - " . $map_data["title"] . " (" . $map_data["creator"] . ").osz"), file_get_contents($path));
      BeatmapController::DownloadBeatmap((object)$map_data, false, true);
    }

    function registerUploadedBeatmap(Request $req) {
      if(!file_exists("/katakuna/beatmap-calculator/index.js")) {
        return redirect('beatmaps.add')
                    ->withErrors(["Katakuna Beatmap Calculator is not installed! Cannot continue."]);
      }

      $validator = Validator::make($req->all(), [
          'BeatmapFile' => 'required|file'
      ]);

      if ($validator->fails()) {
        return redirect('beatmaps.add')
                    ->withErrors($validator);
      }

      $beatmap = $req->file("BeatmapFile");
      BeatmapController::UploadedBeatmapRegister($beatmap->path());

      return view("admin.beatmap-add", ["message" => "Beatmap registered successfully!"]);
    }
}
