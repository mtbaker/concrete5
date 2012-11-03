<?php      
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getCurrentPage();

$playerID = uniqid();

//var_dump(get_defined_vars());

if ($c->isEditMode()) {
?>
<div class="snowReportContainer">
    <h2><?php echo $title; ?></h2>
    <div class="ccm-edit-mode-disabled-item" style="padding: 5em 0;">
        <?php echo t('Content disabled in edit mode.'); ?>
    </div>
</div>
<?php
  return;
} // End of edit mode content

?>

<div class="snowReportContainer">

    <h2><?= $title ?></h2>

    <div class="snowReport">
        <?= nl2br($Summary) ?>
    </div>

</div>