<?php
namespace App;

class OsuUtils {
  public static function StringBinary($str) {
  	return "\x0B" . pack('C', strlen($str)) . $str;
  }

  public static function ReadStringBinary($raw_data, $offset = 0) {
    $data = substr($raw_data, $offset);

    if($data[0] == "\x0B" && $data[1] != "\x00") {
      return substr($data, 2, unpack('C', $data, 1)[1]);
    }

    return "";
  }
}
?>
