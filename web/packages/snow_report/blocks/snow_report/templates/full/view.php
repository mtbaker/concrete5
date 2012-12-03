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
        <p>Please call the snow phone at <strong>(360) 671-0211</strong></p>
    </div>

</div>
<?php
  return; // end of error
}

// Data Available:
//
//    operations
//    hours
//    snowfall24
//    snowfall24_note
//    snowfallnew
//    snowfallnew_note
//    temperature
//    temperature_note
//    winds
//    weather
//    baseheather
//    basepan
//    conditions
//    summary
//    other
//    reporttime
//    submittime
//    type
//    web
//    email
//    fax
//    status
//    morning
//    published

$styleImperial = "";
$btnImperial = "active";
$styleMetric = "display: none;";
$btnMetric = "";

if ($reportUnits == SnowReportBlockController::unitsMetric) {
  $styleImperial = "display: none;";
  $btnImperial = "";
  $styleMetric = "";
  $btnMetric = "active";
}


?>

<div class="snowReportContainer">


                    <table style="width: 540px;" border="0">
                        <tbody>
                            <tr>
                                <td><img src="http://mtbaker.us/files/2213/5302/0607/Baker_logo.gif" alt="" width="181" height="31"></td>
                                <td style="text-align: right;">
                                    <span style="font-size: medium;"><strong><?= $reportday ?> • <?= $reporttime ?></strong></span><br>
                                    <span style="font-size: medium;"><strong><?= $reportdate ?></strong></span><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="float: right;">
                                        <div class="bbtn-group reportUnitsToggle">
                                            <button type="button" class="bbtn bbtn-mini <?= $btnImperial; ?>" data-units="<?= SnowReportBlockController::unitsImperial ?>">Imperial</button>
                                            <button type="button" class="bbtn bbtn-mini <?= $btnMetric; ?>" data-units="<?= SnowReportBlockController::unitsMetric ?>">Metric</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table style="width: 540px;" border="0">
                        <tbody>
                            <tr style="background-color: #1e90ff;">
                                <td style="background-color: #1e90ff;" colspan="3"><span style="font-size: large; color: #ffffff;"><strong>NEW SNOW</strong></span></td>
                            </tr>

                            <tr>
                                <td style="background-color: #e0ffff; text-align: center;"><span style="font-size: small;"><em><strong>12 hrs</strong></em></span></td>
                                <td style="background-color: #add8e6; text-align: center;"><span style="font-size: small;"><em><strong>24 hrs</strong></em></span></td>
                                <!--<td style="background-color: #87ceeb; text-align: center;"><span style="font-size: small;"><em><strong>48 hrs</strong></em></span></td>-->
                            </tr>

                            <tr>
                                <td style="background-color: #e0ffff; text-align: center;"><span style="font-size: x-large;"><strong class="reportUnits reportImperial" style="<?= $styleImperial ?>"><?= $snowfallnew ?>"</strong><strong class="reportUnits reportMetric" style="<?= $styleMetric ?>"><?= $snowfallnew_metric ?> cm</strong></span> <?= $snowfallnew_note ?></td>
                                <td style="background-color: #add8e6; text-align: center;"><span style="font-size: x-large;"><strong class="reportUnits reportImperial" style="<?= $styleImperial ?>"><?= $snowfall24 ?>"</strong><strong class="reportUnits reportMetic" style="<?= $styleMetric ?>"><?= $snowfall24_metric ?> cm</strong></span> <?= $snowfall24_note ?></td>
                                <!--<td style="background-color: #87ceeb; text-align: center;"><span style="font-size: large;"><strong>24"</strong></span></td>-->
                            </tr>
                        </tbody>
                    </table>

                    <table style="width: 540px;" border="0">
                        <tbody>
                            <tr style="background-color: #1e90ff;">
                                <td style="background-color: #1e90ff;" colspan="2"><span style="font-size: large; color: #ffffff;"><strong>CONDITIONS</strong></span></td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6;"><span style="font-size: small;"><em><strong>Temperature</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: medium;">
                                    <strong class="reportUnits reportImperial" style="<?= $styleImperial ?>"><?= $temperature ?>°</strong>
                                    <strong class="reportUnits reportMetric" style="<?= $styleMetric ?>"><?= $temperature_metric ?>°C</strong>
                                    <?= $temperature_note ?></span>
                                </td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6;"><span style="font-size: small;"><em><strong>Base Heather Meadows</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: medium;"><strong class="reportUnits reportImperial" style="<?= $styleImperial ?>"><?= $baseheather ?>"</strong><strong class="reportUnits reportMetric" style="<?= $styleMetric ?>"><?= $baseheather_metric ?> cm</strong></span></td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6;"><span style="font-size: small;"><em><strong>Base Pan Dome</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: medium;"><strong class="reportUnits reportImperial" style="<?= $styleImperial ?>"><?= $basepan ?>"</strong><strong class="reportUnits reportMetric" style="<?= $styleMetric ?>"><?= $basepan_metric ?> cm</strong></span></td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6;"><span style="font-size: small;"><em><strong>Weather</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: medium;"><strong><?= $weather ?></strong></span></td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6;"><span style="font-size: small;"><em><strong>Slope Conditions</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: medium;"><strong><?= $conditions ?></strong></span></td>
                            </tr>
                        </tbody>
                    </table>

                    <table style="width: 540px;" border="0">
                        <tbody>
                            <tr style="background-color: #000000;">
                                <td style="background-color: #1e90ff;" colspan="2"><span style="font-size: large; color: #ffffff;"><strong>OPERATIONS</strong></span></td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6;"><span style="font-size: small;"><em><strong>Hours</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: medium;"><strong><?= $hours ?></strong></span></td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6;"><span style="font-size: small;"><em><strong>Operation</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: medium;"><strong><span style="font-size: small;"><?= $operations ?></span></strong></td>
                            </tr>

                            <tr>
                                <td style="background-color: #add8e6; vertical-align:text-top;"><span style="font-size: small;"><em><strong>Notes</strong></em></span></td>
                                <td style="background-color: #e0ffff;"><span style="font-size: small;"><strong><?= nl2br($summary) ?></strong></span></td>
                            </tr>
                        </tbody>
                    </table>
<? if ("" !== $other) { ?>
                    <table style="width: 540px;" border="0">
                        <tbody>
                            <tr style="background-color: #1e90ff;">
                                <td><span style="font-size: large; color: #ffffff;"><strong>NEWS FROM THE MOUNTAIN<br></strong></span></td>
                            </tr>

                            <tr>
                                <td>
                                    <span style="font-size: small;">
                                        <?= nl2br($other) ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
<? } // end $other if ?>

</div>

<!-- BEGIN POLLING -- lastupdated|<?= $pollingdate ?>|~ newstormtotal|<?= $snowfallnew; ?> in.|~ 24hourtotal|<?= $snowfall24; ?> in.|~ current_temp_base|<?= $temperature ?> F|~ current_temp_summit|<?= $temperature ?> F|~ current_weather_type|<?= $weather; ?>|~ surface_condition_primary|<?= $conditions; ?>|~ surface_depth_base|<?= $baseheather; ?> in.|~ surface_depth_summit|<?= $basepan; ?> in.|~ winds_type|{<?= $winds ?>}|~ operations_hoursofweekday|<?= $hours; ?>|~ specialevents|<?= nl2br($summary); ?>|~   END POLLING -->
