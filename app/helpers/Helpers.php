<?php
class Helpers{
  public static function currency($value)
  {
    return str_replace(" ", "\xc2\xa0", number_format($value, 0, "", " ")) . "\xc2\xa0Kč";
  }

  public static function czechDate($usDate)
  {
    list($year, $month, $day) = explode("-", $usDate);
    if(substr($day, 0, 1) == 0) {
      $day = substr($day,1);
    }
    if(substr($month, 0, 1) == 0) {
      $month = substr($month,1);
    }

    return $day.".".$month.".".$year;
  }

  public static function czechTime($usTime)
  {
    list($hour, $minute) = explode(":", $usTime);
    if(substr($hour, 0, 1) == 0) {
      $hour = substr($hour,1);
    }
    if(substr($minute, 0, 1) == 0 && $minute != "00") {
      $minute = substr($minute,1);
    }

    return $hour.":".$minute;
  }

  public static function round3($value)
  {
    return round($value, 3);
  }
}

