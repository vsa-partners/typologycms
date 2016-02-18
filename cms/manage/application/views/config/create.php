<? $this->load->view('shared/content_header', array('title' => 'Create Config Item')); ?>
	
<form class="tndr_form" method="post" name="editForm" id="editForm" class="edit_form" action="<?=$this->admin_path.$this->module?>/update/">

	<div class="form_row">
		<label>Zone:</label>
		<div class="field"><input type="text" name="fields[zone]" class="required" /></div>
	</div>
	
	<div class="form_row">
		<label>Zone Group: (Optional)</label>
		<div class="field"><input type="text" name="fields[zone_group]" /></div>
	</div>
	
	<div class="form_row">
		<label>Name:</label>
		<div class="field"><input type="text" name="fields[name]" class="required" /></div>
	</div>
	
	<div class="form_row">
		<label>Value:</label>
		<div class="field"><input type="text" name="fields[value]" class="required" /></div>
	</div>
	<div class="form_row">
		<label>Options:</label>
		<div class="field"><input type="text" name="fields[options]" /></div>
		<div class="note">E.g. {"input_type":"file", "multi":"yes", "note":"Top Logo"}</div>
	</div>

	<input type="hidden" name="create" value="1" />
		
	<button type="submit" class="button"><span>SAVE</span></button>
		
</form>