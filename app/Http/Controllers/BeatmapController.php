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

class BeatmapController extends Controller
{
    public static function DownloadBeatmap($query, $set = false, $direct_import = false) {
      if(!$direct_import) {
        $maps = json_decode(file_get_contents("http://bloodcat.com/osu/?mod=json&c=" . ($set ? "s" : "o") . "&s=0,1,2,3&m=0,1,2,3&q=" . urlencode($query) . "&p=1"));
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
      $ranked = 1;
      $popular_maps = false;

      switch ($req->get("r")) {
		    // Ranked/Ranked played (Ranked)
		    case 0:
		    case 7:
			     $ranked = '1';
		       break;
			  // Qualified (Qualified)
		    case 3:
			     $ranked = '3';
			     break;
		    // Pending/Help (Approved)
		    case 2:
			     $ranked = '2';
			     break;
		    // Graveyard (Unranked)
		    case 5:
			     $ranked = '0';
			     break;
		    // All
		    case 4:
		       $ranked = '0,1,2,3';
		       break;
	    }

      $mode = $_GET["m"];

      if($mode == -1) {
        $mode = '0,1,2,3';
      }

      if (isset($_GET['q']) && !empty($_GET['q'])) {
    		if ($req->get("q") == 'Top Rated' || $req->get("q") == 'Most Played') {
    			$popular_maps = true;
    		} elseif ($req->get("q") == 'Newest') {
    			$search_term = '';
    		} else {
    			$search_term = $req->get("q");
    		}
    	} else {
    		$search_term = '';
    	}

      $page = $req->get("p") + 1;

      $search_term = str_replace(" ", '+', "$search_term");
	    $apiURL = $popular_maps ? "http://bloodcat.com/osu/popular.php?mod=json&m=$mode&p=$page" : "http://bloodcat.com/osu/?mod=json&m=$mode&s=$ranked&q=$search_term&p=$page";

      $maps = json_decode(file_get_contents($apiURL));

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
      $user = User::where([["username", "=", $username], ["password_hash", "=", $_POST["pass"]]])->get()->first();
      $bm = BeatmapSet::where("md5", "=", $scoreDataArray[0])->get()->first();
      if(!$bm) {
        return "error: beatmap";
      }

      if(!$user) {
        return "error: beatmap";
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
      $beatmap_ranking->url = "https://osu.ppy.sh/b/" . $bm->beatmap->id;
      $beatmap_ranking->rankBefore = $bm->positionOfUser($user);

      $last_good_play = $bm->plays()->where("user_id", "=", $user->id)->orderBy("maxCombo", "DESC")->get()->first();

      if($last_good_play) {
        $beatmap_ranking->maxComboBefore = $last_good_play->maxCombo;
        $beatmap_ranking->accuracyBefore = $last_good_play->accuracy() * 100;
        $beatmap_ranking->rankedScoreBefore = $last_good_play->score;
        $beatmap_ranking->totalScoreBefore = $last_good_play->score;
        $beatmap_ranking->ppBefore = 0;
      }
      $beatmap_ranking->ppAfter = 0;

      $overall_ranking = new Chart();
      $overall_ranking->id = "overall";
      $overall_ranking->name = "Global Ranking";
      $overall_ranking->url = "https://osu.ppy.sh/u/" . $user->id;
      $overall_ranking->rankBefore = $user->currentRankingPosition($gameMode);
      $overall_ranking->accuracyBefore = $user->accuracy($gameMode);
      $overall_ranking->rankedScoreBefore = $user->totalScore($gameMode);
      $overall_ranking->totalScoreBefore = $user->totalScore($gameMode);
      $overall_ranking->ppBefore = 0;
      $overall_ranking->ppAfter = 0;

      // this is how you do an achievement :D

      // $achv = [
      //     "Icon" => "osu-combo-2000",
      //     "DisplayName" => "YEE",
      //     "Description" => "YEE BOII U SMAERT!"
      // ];
      //
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

      $beatmap_ranking->onlineScoreId = $play->id;
      $beatmap_ranking->rankAfter = $bm->positionOfUser($user);
      $beatmap_ranking->totalScoreAfter = $play->score;
      $beatmap_ranking->rankedScoreAfter = $play->score;
      $beatmap_ranking->accuracyAfter = $play->accuracy() * 100;
      $beatmap_ranking->maxComboAfter = $play->maxCombo;

      \Artisan::call('schedule:run');

      return "beatmapId:" .
        $bm->beatmap->id .
        "|beatmapSetId:" .
        $bm->id .
        "|beatmapPlaycount:" .
        count($user->played_scores()->where("beatmapset_id", "=", $bm->id)->get()) .
        "|beatmapPasscount:" .
        count($user->played_scores()->where([["beatmapset_id", "=", $bm->id], ["pass", "=", "1"]])->get()) .
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
                $map_data["id"] = BeatmapController::GetOsuBMMetadata($_beatmap->getFromIndex($i), "BeatmapSetID");
              }
          }
      }

      Storage::put(BeatmapController::FileNameClean("beatmaps/" . $map_data["artist"] . " - " . $map_data["title"] . " (" . $map_data["creator"] . ").osz"), file_get_contents($path));
      BeatmapController::DownloadBeatmap((object)$map_data, false, true);
    }

    function registerUploadedBeatmap(Request $req) {
      if(!file_exists("/katakuna/beatmap-calculator/index.js")) {
        return redirect('/add-beatmap')
                    ->withErrors(["Katakuna Beatmap Calculator is not installed! Cannot continue."]);
      }

      $validator = Validator::make($req->all(), [
          'BeatmapFile' => 'required|file'
      ]);

      if ($validator->fails()) {
        return redirect('/add-beatmap')
                    ->withErrors($validator);
      }

      $beatmap = $req->file("BeatmapFile");
      BeatmapController::UploadedBeatmapRegister($beatmap->path());

      return view("website.beatmap-add", ["message" => "Beatmap registered successfully!"]);
    }
}
