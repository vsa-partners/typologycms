<? $this->load->view('shared/content_header', array('title' => 'Edit Page XML', 'buttons' => array('back'))); ?>

<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$fields['module'].'/update/'.$this->current_id?>" accept-charset="utf-8">

	<input type="hidden" name="fields[page_id]" value="<?=$fields['page_id']?>" />

	<fieldset class="open">
		<legend>Page XML</legend>
		<textarea name="fields[content]" style="width: 600px; height: 300px; font-family: courier;"><?=htmlentities($fields['content'], ENT_NOQUOTES, 'UTF-8');?></textarea>
	</fieldset>
	
	<div id="page_edit_buttons">
		<button id="submit_draft" name="submit_draft" value="update_draft" class="button button_outline" type="submit"><span>SAVE DRAFT</span></button>
	</div>

</form>

<? $this->load->view('shared/content_footer'); ?>

<!-- END PAGE EDIT -->