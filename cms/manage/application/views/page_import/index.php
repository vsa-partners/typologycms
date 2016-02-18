<div class="content_header">
	<h2>Content Import</h2>
</div>

<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/confirm'?>" accept-charset="utf-8" enctype="multipart/form-data">
	<div class="form_row">
		<label>File:</label>
		<div class="field"><input class="required" type="file" name="userfile"  /></div>
	</div>
	<div class="form_row">
		<label>Method:</label>
		<div class="field">
			<select name="settings[method]" class="toggle_hide" showif="create" toshow="fields_template_id,fields_parent_id">
				<option value="update">Update</option>
				<option value="create">Create</option>
			</select>
		</div>
	</div>

	<div class="form_row" id="fields_template_id" style="display:none;">
		<label>Template:</label>
		<div class="field">
			<?=form_dropdown('settings[template_id]', $templates)?>
		</div>
	</div>

	<div class="form_row" id="fields_parent_id" style="display:none;">
		<label>Parent:</label>
		<div class="field" style="width:327px;">
			<input type="hidden" name="settings[parent_id]" id="settings[parent_id]" value="" class="required" />
			<input type="hidden" name="fields_parent_id_path_trash" id="settings[parent_id][page_path]" value="" />
			<div class="field_buttons">
				<button type="button" class="button button_outline_small" onClick="this.blur(); itemPicker.open({'module': 'page', 'destination': 'settings[parent_id]'});"><span>CHOOSE</span></button>
			</div>
			<div class="field_text" id="settings[parent_id]_display">Select Folder</div>	
		</div>
	</div>

	<div class="form_row">
		<div class="field" style="padding: 10px 0 0 0;"><button type="submit" class="button"><span>Upload</span></button></div>
	</div>
		
</form>

<div class="clear"> </div>
