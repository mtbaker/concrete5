<?php      
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getCurrentPage();

$playerID = uniqid();

// var_dump(get_defined_vars());

if ($c->isEditMode()) {
?>
<div class="snowReportContainer">
    <h2><?php echo $title; ?></h2>
    <div class="ccm-edit-mode-disabled-item" style="padding: 5em 0;">
        <?php echo t('Content disabled in edit mode.'); ?>
    </div>
</div>
<?php
  return; // end of edit mode
} elseif ($dbError === true) {
?>
<div class="snowReportContainer">

    <h2>Sorry, we weren't able to load the snow report.</h2>

    <div class="snowReport">
        <p>Please call the snow phone at <strong>(360) 734-6771</strong></p>
    </div>

</div>
<?php
  return; // end of error
}

?>

<div class="snowReportContainer">

    <h2><?= $title ?></h2>

    <div class="snowReport">
        <p><?= $Weather ?></p>
        <p><?= nl2br($Summary) ?></p>
    </div>

</div>