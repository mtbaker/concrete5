<? defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
Loader::model('attribute/categories/file');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}


Loader::model('file_list');
$selectedAKIDs = array();

$fldc = $u->config('FILE_LIST_DEFAULT_COLUMNS');
if (!($fldc instanceof DatabaseItemListColumnSet)) {
	$fldc = new FileManagerDefaultColumnSet();
}

$fldca = new FileManagerAvailableColumnSet();

$searchInstance = $_REQUEST['searchInstance'];

if ($_POST['task'] == 'update_columns') {
	Loader::model('attribute/category');
	$sc = AttributeKeyCategory::getByHandle('file');
	$sc->clearAttributeKeyCategoryColumnHeaders();
	
	if (is_array($_POST['akID'])) {
		foreach($_POST['akID'] as $akID) {
			$ak = FileAttributeKey::getByID($akID);
			$ak->setAttributeKeyColumnHeader(1);
		}
	}
	
	exit;
}

$list = FileAttributeKey::getList();

?>

<form method="post" id="ccm-<?=$searchInstance?>-customize-search-columns-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns/">
<?=$form->hidden('task', 'update_columns')?>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="35%" valign="top">
	<h1><?=t('Choose Headers')?></h1>
	
	<h2><?=t('Standard Properties')?></h2>
	
	<?
	$columns = $fldca->getColumns();
	foreach($columns as $col) { 

		?>

		<div><?=$form->checkbox($col->getColumnKey(), 1, $fldc->contains($col), array('style' => 'vertical-align: middle'))?> <label for="<?=$col->getColumnKey()?>"><?=$col->getColumnName()?></label></div>
	
	<? } ?>
	
	<h2><?=t('Additional Attributes')?></h2>
	
	<? foreach($list as $ak) { ?>
	
		<div><?=$form->checkbox('akID[]', $ak->getAttributeKeyID(), $fldc->contains($ak), array('style' => 'vertical-align: middle'))?> <label for="akID_<?=$ak->getAttributeKeyID()?>"><?=$ak->getAttributeKeyDisplayHandle()?></label></div>
		
	<? } ?>
	
	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td valign="top" width="65%">
	
	<h1><?=t('Column Order')?></h1>
	
	<p><?=t('Click and drag to change column order.')?></p>
	
	<ul class="ccm-search-sortable-column-wrapper" id="ccm-<?=$searchInstance?>-sortable-column-wrapper">
	<? foreach($fldc->getColumns() as $col) { ?>
		<li id="field_<?=$col->getColumnKey()?>"><?=$col->getColumnName()?></li>	
	<? } ?>	
	</ul>
	
	<h1><?=t('Sort By')?></h1>
	
	<div class="ccm-sortable-column-sort-controls">
	
	<? $ds = $fldc->getDefaultSortColumn(); ?>
	
	<select <? if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?=$searchInstance?>-sortable-column-default" name="fSearchDefaultSort">
	<? foreach($fldc->getSortableColumns() as $col) { ?>
		<option id="opt_<?=$col->getColumnKey()?>" <? if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="true" <? } ?>><?=$col->getColumnName()?></option>
	<? } ?>	
	</select>
	<select <? if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?=$searchInstance?>-sortable-column-default-direction" name="fSearchDefaultSortDirection">
		<option value="asc" <? if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <? } ?>><?=t('Ascending')?></option>
		<option value="desc" <? if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <? } ?>><?=t('Descending')?></option>	
	</select>	
	</div>
	
	</td>
</tr>
</table>

<br/><br/>
<?
$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Save'), 'ccm_submitCustomizeSearchColumnsForm()');
print $b1;
?>

</form>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	var fslist = $('#ccm-<?=$searchInstance?>-sortable-column-wrapper').sortable('serialize');
	$('input[name=fSearchDisplayOrder]').val(fslist);
	alert(fslist);
		
	ccm_deactivateSearchResults('<?=$searchInstance?>');
	$("#ccm-<?=$searchInstance?>-customize-search-columns-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, '<?=$searchInstance?>');
		});
	});
}

$(function() {
	$('#ccm-<?=$searchInstance?>-sortable-column-wrapper').sortable({
		cursor: 'move',
		opacity: 0.5
	});
	$('form#ccm-<?=$searchInstance?>-customize-search-columns-form input[type=checkbox]').click(function() {
		var thisID = $(this).attr('id').replace(/_/,'');
		var thisLabel = $(this).parent().find('label').html();
		if ($(this).attr('checked')) {
			if ($('#field_' + thisID).length == 0) {
				$('#ccm-<?=$searchInstance?>-sortable-column-default').append('<option value="' + thisID + '" id="opt_' + thisID + '">' + thisLabel + '<\/option>');
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', false);
				$('#ccm-<?=$searchInstance?>-sortable-column-wrapper').append('<li id="field_' + thisID + '">' + thisLabel + '<\/li>');
			}
		} else {
			$('#field_' + thisID).remove();
			$('#opt_' + thisID).remove();
			if ($('#ccm-<?=$searchInstance?>-sortable-column-wrapper li').length == 0) {
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', true);
			}
		}
	});
	$('#ccm-<?=$searchInstance?>-customize-search-columns-form').submit(function() {
		ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>