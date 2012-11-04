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
        <p>Published <?= $submittimeago ?> ago</p>
        <p><?= $weather ?></p>
        <p><?= nl2br($summary) ?></p>
    </div>

</div>

<!-- BEGIN POLLING -- lastupdated|<?= $pollingdate ?>|~ newstormtotal|<?= $snowfallnew; ?> in.|~ 24hourtotal|<?= $snowfall24; ?> in.|~ current_temp_base|<?= $temperature ?> F|~ current_temp_summit|<?= $temperature ?> F|~ current_weather_type|<?= $weather; ?>|~ surface_condition_primary|<?= $conditions; ?>|~ surface_depth_base|<?= $baseheather; ?> in.|~ surface_depth_summit|<?= $basepan; ?> in.|~ winds_type|{<?= $winds ?>}|~ operations_hoursofweekday|<?= $hours; ?>|~ specialevents|<?= nl2br($summary); ?>|~   END POLLING -->
