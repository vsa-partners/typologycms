<? $this->load->view('shared/content_header', array('title' => 'Query'));  ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module_path.'/'.$this->current_table.'/edit/'.$this->current_id?>" accept-charset="utf-8">

	<div class="form_row">
		<label>Query:</label>
		<div class="field">
			<textarea name="query"> </textarea>
		</div>
	</div>
	
	<button id="submit" name="submit" value="submit" class="button" type="submit"><span>SUBMIT</span></button>

</form>
