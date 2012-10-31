<?php      
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getCurrentPage();

$playerID = uniqid();
?>
<div class="snowReportContainer">

<?php if (strlen($title) > 0) { ?>
    <h2><?php echo $title; ?></h2>
<?php } ?>
  
<?php if ($c->isEditMode()) { ?>
    <div class="ccm-edit-mode-disabled-item">
        <?php echo t('Content disabled in edit mode.'); ?>
    </div>
<?php } else { ?>
    <div class="snowReport">
        <?= $depth ?>
    </div>
<?php } ?>
</div>