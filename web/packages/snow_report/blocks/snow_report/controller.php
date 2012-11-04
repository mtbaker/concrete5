<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

require __DIR__ . "/../../lib/SnowReportConverter.php";
require __DIR__ . "/../../lib/SnowReportException.php";

class SnowReportBlockController extends BlockController {

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
    $report_db = Loader::db($this->host, $this->username, $this->password, $this->databasename, true);

    try {
      $results = &$report_db->Execute("
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
          web = 1
        order by ReportID DESC
        limit 1
      ");
    }
    catch (ADODB_Exception $e) {
      // ADOdb threw an exception
      throw new SnowReportException($e->getMessage(), $e->getCode(), $e);
    }

    if ($results === false) {
      // No result means the query was bad
      throw new SnowReportException($report_db->ErrorMsg());
    }
    
    if ($results->fields === false) {
      // Empty result set
      throw new SnowReportException("No matching report");
    }

    // This loads all the results from the query into the object
    // we're going to return. We'll also decorate this with some
    // other data, like metric and nice timestamps
    $report = (object) $results->fields;

    // Clean up the result sets, C5 says this is optional, I don't trust them.
    $results->Close();
    $report_db->Close();

    // Metric conversions
    $report->Temperature_metric = SnowReportConverter::toCelcius($report->Temperature);

    $report->Snowfall24_metric  = SnowReportConverter::toCentimeters($report->Snowfall24);
    $report->SnowfallNew_metric = SnowReportConverter::toCentimeters($report->SnowfallNew);

    $report->BaseHeather_metric = SnowReportConverter::toCentimeters($report->BaseHeather);
    $report->BasePan_metric     = SnowReportConverter::toCentimeters($report->BasePan);

    // Friendly time format, included the "ago"
    $date_helper = Loader::helper('date');
    $report->SubmitTimeAgo = $date_helper->timeSince($report->SubmitTime);
    $report->ReportDay     = $date_helper->date("F j", $report->SubmitTime);

    return $report;
  }

  public function view() {
    $this->set('dbError', false);

    // We're going to use our own error handling here so that
    // we can show an error template with a snow phone number on it.
    try {
      $report = $this->fetchReport();
    } catch (SnowReportException $e) {
      // Send an email to admins, max once per hour
      $e->sendThrottledEmail($this->failurecontacts);
      // Change the block lifetime so we try again soon.
      $this->btCacheBlockOutputLifetime = 15;
      // the template can change messages based on this.
      $this->set('dbError', true);
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
    $formVars = array(
        'title', 'host', 'databasename', 'username', 'password', 'failurecontacts'
    );

    $args = array();
    foreach ($formVars as $var) {
      $args[$var] = isset($data[$var]) ? trim($data[$var]) : '';
    }

    parent::save($args);
  }
}

/* End of file controller.php */
