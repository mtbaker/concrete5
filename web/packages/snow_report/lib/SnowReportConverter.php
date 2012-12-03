<?php

class SnowReportConverter {

  static $precision = 1;

  static public function toCelcius($temp) {
    return round(($temp - 32) * 0.5556, self::$precision);
  }

  static public function toCentimeters($length) {
    return round($length * 2.54, self::$precision);
  }

}

/* End of file SnowReportConverter.php */
