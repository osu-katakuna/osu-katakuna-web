<?php
namespace App\Osu;

class Chart {

  public $id = "";
  public $url = "";
  public $name = "";
  public $rankBefore = 0;
  public $rankAfter = 0;
  public $maxComboBefore = 0;
  public $maxComboAfter = 0;
  public $accuracyBefore = 0;
  public $accuracyAfter = 0;
  public $rankedScoreBefore = 0;
  public $rankedScoreAfter = 0;
  public $totalScoreBefore = 0;
  public $totalScoreAfter = 0;
  public $ppBefore = 0;
  public $ppAfter = 0;
  public $onlineScoreId = 0;
  private $achievements = array();

  public function addAchievement($achv) {
    array_push($this->achievements, $achv);
  }

  private function GetAchievementString() {
    $str = "";
    foreach($this->achievements as $a) {
      $str .= $a["Icon"] . "+" . $a["DisplayName"] . "+" . $a["Description"] . "/";
    }

    return substr($str, 0, strlen($str) - 1);
  }

  public function ToString() {
    return "chartId:" . $this->id .
       "|chartUrl:" . $this->url .
       "|chartName:" . $this->name .
       "|rankBefore:" . $this->rankBefore .
       "|rankAfter:" . $this->rankAfter .
       "|maxComboBefore:" . $this->maxComboBefore .
       "|maxComboAfter:" . $this->maxComboAfter .
       "|accuracyBefore:" . $this->accuracyBefore .
       "|accuracyAfter:" . $this->accuracyAfter .
       "|rankedScoreBefore:" . $this->rankedScoreBefore .
       "|rankedScoreAfter:" . $this->rankedScoreBefore .
       "|totalScoreBefore:" . $this->totalScoreAfter .
       "|totalScoreAfter:" . $this->totalScoreAfter .
       "|ppBefore:" . $this->ppBefore .
       "|ppAfter:" . $this->ppAfter .
       (count($this->achievements) < 1 ? "" : "|achievements-new:" . $this->GetAchievementString()) .
       "|onlineScoreId:" . $this->onlineScoreId;
  }

}
?>
