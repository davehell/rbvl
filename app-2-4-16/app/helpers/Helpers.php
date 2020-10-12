<?php
class Helpers{
  public static function currency($value)
  {
    return str_replace(" ", "\xc2\xa0", number_format($value, 0, "", " ")) . "\xc2\xa0Kč";
  }

  public static function czechDate($usDate)
  {
    return $usDate->format("j. n. Y");
  }

  public static function czechTime($dateInterval)
  {
    return $dateInterval->format("%H:%I");
  }

  public static function round3($value)
  {
    return round($value, 3);
  }

  public static function aktuality($string)
  {
    $string = preg_replace('/={2,}/i', "<br>", $string);
    $string = preg_replace('/#{2,}/i', "<br>", $string);
    $string = preg_replace('/\*{2,}/i', "<br>", $string);
    $string = preg_replace('/-{2,}/i', "", $string);
    $string = preg_replace('/\|/i', "", $string);
    $string = preg_replace('/^<br>/i', "", $string);
    return $string;
  }

  public static function vlna($string)
  {
    $string = preg_replace('<([^a-zA-Z0-9])([ksvzaiou])\s([a-zA-Z0-9]{1,})>i', "$1$2\xc2\xa0$3", $string); //&nbsp; === \xc2\xa0
    return $string;
  }
}

