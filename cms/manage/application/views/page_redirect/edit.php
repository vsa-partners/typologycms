<?

// ------------------------------------------------------------------------

?>
	
<? $this->load->view('shared/content_header', array('title' => 'Edit Redirect', 'buttons' => array('delete'))); ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/update/'.$this->current_id?>">

	<input type="hidden" name="fields[<?=$this->id_field?>]" value="<?=$fields[$this->id_field]?>" />
	<input type="hidden" name="fields[type]" value="redirect" />

	<div class="form_row">
		<label>Old Url:</label>
		<div class="field"><input class="required" type="text" name="fields[old_path]" value="<?=$fields['old_path']?>" /></div>
		<div class="note">Do not include domain. Exmaple: 'http://www.website.com/old_page' should be entered as '/old_page'</div>
	</div>

	<div class="form_row">
		<label>New Url:</label>
		<div class="field"><input class="required" type="text" name="fields[new_path]" value="<?=$fields['new_path']?>" /></div>
	</div>

	<div class="form_row">
		<label>Notes:</label>
		<div class="field"><textarea name="fields[notes]" id="fields[notes]"><?=$fields['notes']?></textarea></div>
		<div class="note">Optional. Internal reference only.</div>
	</div>

	<!-- START MODULE EDIT -->

	<button type="submit" class="button"><span><?=(($fields[$this->id_field] < 0) ? 'CREATE' : 'SAVE')?></span></button>

</form>


<? $this->load->view('shared/content_footer'); ?>

<!-- END PAGE EDIT -->
