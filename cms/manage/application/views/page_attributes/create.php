<div class="content_header"><h2>Create</h2></div>

<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module?>/update/-1">

	<div class="form_row">
		<label>Name:</label>
		<div class="field"><input type="text" name="fields[group_title]" class="required" /></div>
	</div>
	
	<button name="submit_draft" value="update_draft" type="submit" class="button"><span>CREATE</span></button>

</form>
