<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class SnowReportBlockController extends BlockController {

  var $pobj;

  protected $btTable = 'btSnowReport';
  protected $btInterfaceWidth = "600";
  protected $btInterfaceHeight = "400";
  protected $btCacheBlockOutput = false;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 300;

  public $title = '';

  public function getBlockTypeDescription() {
    return t("Embeds a snow report in your web page.");
  }

  public function getBlockTypeName() {
    return t("Snow Report");
  }

  public function fetchReport() {

    // Concrete5 uses an [ADOdb library](http://adodb.sourceforge.net/)
    $db = Loader::db($this->host, $this->username, $this->password, $this->databasename, true);

    $results = &$db->Execute("
      select
        ReportID
        , Operations
        , Hours
        , Snowfall24
        , Snowfall24_note
        , SnowfallNew
        , SnowfallNew_note
        , Temperature
        , Temperature_note
        , Winds
        , Weather
        , BaseHeather
        , BasePan
        , Conditions
        , Summary
        , Other
        , ReportTime
        , SubmitTime
        , UserID
        , Web
        , Email
        , Fax
        , Status
        , Type
      from tbl_Reports
      where
        status = 1 and
        web = 1 and web =0
      order by ReportID DESC
      limit 1
    ");

    if ($results === false) {
      throw new SnowReportException();
    }

    // This loads all the results from the query into the object
    // we're going to return, but next we'll decorate this with some
    // other data, like metric and nice timestamps
    $report = (object) $results->fields;

    // Metric conversions
    $report->Temperature_metric = SnowReportBlockConverter::toCelcius($report->Temperature);

    $report->Snowfall24_metric  = SnowReportBlockConverter::toCentimeters($report->Snowfall24);
    $report->SnowfallNew_metric = SnowReportBlockConverter::toCentimeters($report->SnowfallNew);

    $report->BaseHeather_metric = SnowReportBlockConverter::toCentimeters($report->BaseHeather);
    $report->BasePan_metric     = SnowReportBlockConverter::toCentimeters($report->BasePan);

    // Friendly time format, included the "ago"
    $date_helper = new Concrete5_Helper_Date();
    $report->SubmitTimeAgo = $date_helper->timeSince($report->SubmitTime);
    $report->ReportDay     = $date_helper->date("F j", $report->SubmitTime);

    $results->Close();
    $db->Close();

    return $report;
  }

  public function view() {
    try {
      $report = $this->fetchReport();
    } catch (SnowReportException $e) {
      $e->sendThrottledEmail();
      // TODO: change to error template
      return;
    }

    // From the C5 database
    $this->set('bID', $this->bID);
    $this->set('title', $this->title);

    // Expose each of the results from the database to the template
    foreach ($report as $key => $value) {
      $this->set($key, $value);
    }

    return;
  }

  public function save($data) {
    // See the db.xml document for more info about these.
    $formVars = array('title', 'host', 'databasename', 'username', 'password');

    $args = array();
    foreach ($formVars as $var) {
      $args[$var] = isset($data[$var]) ? trim($data[$var]) : '';
    }

    parent::save($args);
  }
}

class SnowReportException extends exception {

  public function __construct($message = null, $code = 0, Exception $previous = null) {
    // TODO: write to error log
    // http://www.concrete5.org/documentation/developers/system/logging
    
    parent::__construct($message, $code, $previous);
  }
  
  public function sendThrottledEmail() {
      // TODO
      // check for a error.txt file less than an hour old
      // if not found, send mail to contacts (see if there is a c5 setting)
      
      // touch the file
  }

  public function sendEmail() {
    // TODO
    // http://www.concrete5.org/documentation/developers/helpers/mail
  }
}

class SnowReportBlockConverter {

  static public function toCelcius($temp) {
    return ($temp - 32) * 0.5556;
  }

  static public function toCentimeters($length) {
    return $length * 2.54;
  }

}

/* End of file controller.php */