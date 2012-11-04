<?php      
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getCurrentPage();

$playerID = uniqid();

if ($c->isEditMode()) {
?>
<div class="snowReportContainer">
    <h2><?= $title ?></h2>
    <div class="ccm-edit-mode-disabled-item" style="padding: 1em 0;">
        <?php echo t('Content disabled in edit mode.'); ?>
    </div>
</div>
<?php
  return;
}
?>

<div class="snowReportContainer">
    <div class="snowReportSmall">
        <?= $temperature ?>&deg;

        <p><small>Published <?= $submittimeago ?> ago</small></p>
    </div>
</div>