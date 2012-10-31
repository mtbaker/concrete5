<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class SnowReportPackage extends Package {

  protected $pkgHandle          = 'snow_report';
  protected $appVersionRequired = '5.2.0';
  protected $pkgVersion         = '1.0.0';

  public function getPackageDescription() {
    return t("Embeds a snow report in your web page.");
  }

  public function getPackageName() {
    return t("Snow Report");
  }

  public function install() {
    $pkg = parent::install();
    
    BlockType::installBlockTypeFromPackage('snow_report', $pkg);
  }

}

/* End of file controller.php */
