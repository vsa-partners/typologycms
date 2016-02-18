<?

// Add things that could be empty
if (empty($fields['force_download']))	$fields['force_download'] = 0;

?>

<? $this->load->view('shared/content_header', array('title' => 'Edit File', 'buttons' => array('delete'))); ?>

<form class="tndr_form" method="post" name="editForm" id="editForm" enctype="multipart/form-data" action="<?=$this->admin_path.$this->module.'/update/file/'.$this->current_id?>" accept-charset="utf-8">

	<input type="hidden" name="fields[<?=$this->id_field?>]" value="<?=$fields[$this->id_field]?>" />

	<? if ($fields[$this->id_field] < 0): ?>
		<input type="hidden" name="fields[type]" value="<?=$fields['type']?>" />
	<? endif; ?>
	
	<div class="form_row">
		<label><?=ucwords($fields['type'])?> Title:</label>
		<div class="field"><input class="required" type="text" name="fields[title]" value="<?=$fields['title']?>" /></div>
	</div>

	<div class="form_row">
		<label>Type:</label>
		<div class="field"><div class="field_text"><?=$fields['ext']?> (<?=$fields['mime']?>)</div></div>
	</div>

	<div class="form_row">
		<label>Options:</label>
		<div class="field">
			<? foreach ($fields['options'] as $key => $val): ?>
				<div style="padding: 4px 0;"><strong><?=humanize($key)?>:</strong> <?=$val?></div>
			<? endforeach; ?>
		</div>
	</div>

	<div class="form_row">
		<label>Collection:</label>
		<div class="field"><div class="field_text">
			<img src="<?=$this->asset_path?>img/mini_icons/folder.gif" width="10" height="10"/>
			<span id="fields[parent_id]_display"><a href="<?=$this->admin_path.$this->module?>/edit/<?=$fields['parent_id']?>"><?=$collection['title']?></a></span>
		</div></div>
		<div class="field_button">
			<?
			$js_params = array(
				'module'			=> 'file'
				, 'destination'		=> 'fields[parent_id]'
				, 'select_type'		=> '|collection|'
				);
			?>
			<input class="required" type="hidden" id="fields[parent_id]" name="fields[parent_id]" value="<?=$fields['parent_id']?>" />
			<button type="button" class="button button_outline_small" onClick='itemPicker.open(<?=json_encode($js_params)?>); this.blur();'><span>MOVE</span></button>
		</div>
	</div>

	<div class="form_row">
		<label>Preview:</label>
		<div class="field">
			<? if ($fields['is_image']) {
			
				if ($fields['options']['image_width'] < 480) {
					echo '<img src="'.$fields['manage_path'].'"/>';
				} else {
					echo '<img src="'.$fields['manage_path'].'?w=480"/>';
				}
			} else {
				echo '<div class="field_text"><a href="'.$fields['manage_path'].'">View File</a></div>';
			}
			?>
		</div>
	</div>

	<div class="form_row">
		<label>Replace:</label>
		<div class="field">
			<div id="replace_button" class="field_text">(<a onclick="TNDR.Form.Actions.toggleHideStatic('replace_upload', 'show'); TNDR.Form.Actions.toggleHideStatic('replace_button', 'hide'); return false;" href="#">Click to upload new file</a>)</div>
			<span id="replace_upload" style="display: none;">
				<input type="file" name="userfile" disabled="disabled" />
				<input type="hidden" name="dupe_action" value="replace" />
			</span>
		</div>
	</div>


	<div class="form_row">
		<label>File Size:</label>
		<div class="field"><div class="field_text"><?=$fields['file_size_display']?></div></div>
	</div>

	<div class="form_row">
		<label>File Path:</label>
		<div class="field">
			<div class="field_text">http://<?=$_SERVER["HTTP_HOST"].$fields['view_path']?></div>
		</div>
	</div>

	<? if ($fields['is_image']) : ?>
		<div class="form_row">
			<label>HTML Image Tag:</label>
			<div class="field">
				<? if(!empty($fields['options']['image_size_str'])): ?>
					<input type="text" value="<?=htmlentities('<img src="'.$fields['view_path'].'" '.$fields['options']['image_size_str'].'/>');?>" READONLY style="width: 480px;" />
				<? endif; ?>
			</div>
		</div>
	<? endif; ?>	

	<button type="submit" class="button"><span><?=(($fields[$this->id_field] < 0) ? 'CREATE' : 'SAVE')?></span></button>

	<div class="clear"> </div>
	<fieldset class="closed" id="set_debug">
		<legend><a href="#" onClick="TNDR.Form.Actions.toggleFieldset('set_debug'); return false;">Debug</a></legend>
		<div class="form_row">
			<? pr($fields, 'Fields', TRUE, TRUE); ?>
		</div>
	</fieldset>

</form>

<? $this->load->view('shared/content_footer'); ?>
