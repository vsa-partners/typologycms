<div id="popup_header">ERROR</div>

<div id="popup_content">
	<p>There is already a file uploaded by that name: <br/><br/></p>
	<img src="<?=$existing['view_path']?>?w=80" width="80"/>
</div>


<form class="tndr_form" method="post" name="uploadForm" id="uploadForm" action="<?=$this->admin_path.$this->module?>/update/file/-1" accept-charset="utf-8">

	<div class="form_row">
		<label>Action:</label>
		<div class="field">
			<select name="dupe_action" class="toggle_hide" showif="new" toshow="new_name">
				<option value="replace">Replace Existing File</option>
				<option value="new">Upload as New File</option>
			</select>	
		</div>
	</div>

	<div class="form_row" id="new_name" >
		<label>New Title:</label>
		<div class="field"><input type="text" name="fields[title]" value="<?=$existing['title']?>" /></div>
	</div>

	<div class="form_row last">
		<label>&nbsp;</label>
		<div class="field"><button type="submit" class="button"><span>CONTINUE</span></button></div>
	</div>						

	<input type="hidden" name="existing_id" value="<?=$existing['file_id']?>" />

	<input type="hidden" name="tmp_file" value="<?=$tmp_file?>" />

	<input type="hidden" name="collection" value="<?=$collection?>" />
	<input type="hidden" name="fields[parent_id]" value="<?=$collection?>" />

	<input type="hidden" name="destination" value="<?=$this->input->get_post('destination')?>" />
	<input type="hidden" name="return" value="<?=$this->input->get_post('return')?>" />

</form>
