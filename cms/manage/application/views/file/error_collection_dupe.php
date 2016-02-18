<div id="popup_header">ERROR</div>

<div id="popup_content">
	<p>There is already a folderby that name.</p>
</div>

<div class="fp_buttons">

	<form class="tndr_form" method="post" name="uploadForm" id="uploadForm" action="<?=$this->admin_path.$this->module?>/update/collection/-1" accept-charset="utf-8">

		<div class="form_row" id="new_name" >
			<label>Folder Name:</label>
			<div class="field"><input type="text" class="required" name="fields[title]" value="<?=$fields['title']?>" /></div>
		</div>
	
		<div class="form_row last">
			<label>&nbsp;</label>
			<div class="field"><input type="submit" value="CONTINUE" /></div>
		</div>						
	
		<input type="hidden" name="destination" value="<?=$this->input->get_post('destination')?>" />
		<input type="hidden" name="return" value="<?=$this->input->get_post('return')?>" />
		<input type="hidden" name="fields[parent_id]" value="<?=$fields['parent_id']?>" />
	
	</form>

</div>
