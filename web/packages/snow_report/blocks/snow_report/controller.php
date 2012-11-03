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
    $db = Loader::db($this->host, $this->username, $this->password, $this->database, true);

    $db->Execute('select * from TestTable2');
  
    $report = (object) array(
      'depth' => 8,
      'temp'  => 28
    );
    return $report;
  }

  public function view() {
    $report = $this->fetchReport();

    $this->set('bID', $this->bID);
    $this->set('title', $this->title);

    // Expose each of the results from the database to the template
    foreach ($report as $key => $value) {
      $this->set($key, $value);
    }
  }

  public function save($data) {

    $formVars = array('title', 'host', 'tablename', 'username', 'password');
    $args = array();
    foreach ($formVars as $var) {
      $args[$var] = isset($data[$var]) ? trim($data[$var]) : '';
    }

    parent::save($args);
  }
}

/* End of file controller.php */