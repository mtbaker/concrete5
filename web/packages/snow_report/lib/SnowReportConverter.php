<?php

class SnowReportConverter {

  static public function toCelcius($temp) {
    return ($temp - 32) * 0.5556;
  }

  static public function toCentimeters($length) {
    return $length * 2.54;
  }

}

/* End of file SnowReportConverter.php */
