<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

require __DIR__ . "/../../lib/SnowReportConverter.php";
require __DIR__ . "/../../lib/SnowReportException.php";

class SnowReportBlockController extends BlockController {

  protected $btTable = "btSnowReport";
  protected $btInterfaceWidth = "600";
  protected $btInterfaceHeight = "400";
  protected $btCacheBlockOutput = true;
  protected $btCacheBlockOutputOnPost = true;
  protected $btCacheBlockOutputForRegisteredUsers = false;
  protected $btCacheBlockOutputLifetime = 60;

  public $title = "";

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
          id
          , operations
          , hours
          , snowfall24
          , snowfall24_note
          , snowfallnew
          , snowfallnew_note
          , temperature
          , temperature_note
          , winds
          , weather
          , baseheather
          , basepan
          , conditions
          , summary
          , other
          , reporttime
          , submittime
          , type
          , web
          , email
          , fax
          , status
          , morning
          , published
        from incw_snowconditions
        where
          published = 1 and
          type = 'web'
        order by id DESC
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
    $report->temperature_metric = SnowReportConverter::toCelcius($report->temperature);

    $report->snowfall24_metric  = SnowReportConverter::toCentimeters($report->snowfall24);
    $report->snowfallnew_metric = SnowReportConverter::toCentimeters($report->snowfallnew);

    $report->baseheather_metric = SnowReportConverter::toCentimeters($report->baseheather);
    $report->basepan_metric     = SnowReportConverter::toCentimeters($report->basepan);

    // Friendly time format, included the "ago"
    $date_helper = Loader::helper('date');
    $report->submittimeago = $date_helper->timeSince($report->submittime);
    $report->reportdate    = $date_helper->date("F j, Y", $report->submittime);
    $report->reportday     = $date_helper->date("l", $report->submittime);
    $report->reporttime    = $date_helper->date("g:i a", $report->submittime);

    // Ski WA data
    $report->pollingdate = date('Y-m-d H:i:s', $report->submittime);

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
