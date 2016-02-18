<?

// ------------------------------------------------------------------------

?>
	
<? $this->load->view('shared/content_header', array('title' => 'Edit Attribute Group')); ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/update/'.$this->current_id?>">

	<input type="hidden" name="fields[<?=$this->id_field?>]" value="<?=$fields[$this->id_field]?>" />

	<fieldset>
		<legend>Information</legend>
	
		<div class="form_row">
			<label>Title:</label>
			<div class="field"><input class="required" type="text" name="fields[group_title]" value="<?=$fields['group_title']?>" /></div>
		</div>

		<button type="submit" class="button"><span><?=(($fields[$this->id_field] < 0) ? 'CREATE' : 'SAVE')?></span></button>

	</fieldset>

	<fieldset>
		<legend>Values</legend>

		<div class="fs_content">

			<table class="data_table" width="100%" cellspacing="0" cellpadding="0" border="0">

	         	<? foreach($fields['values'] as $value): ?>

	         		<tr>
	         			<td width="10"><img src="<?=$this->asset_path?>img/mini_icons/tag.gif" width="10" height="10"/></td>
	         			<td><a href="<?=$this->admin_path.$this->module.'/edit_value/'.$value['page_attributevalue_id']?>"><?=$value['value_title']?></a></td>
	         		</tr>
	         	<? endforeach; ?>

	         </table>

		</div>

	</fieldset>


</form>

<? $this->load->view('shared/content_footer'); ?>

<!-- END PAGE EDIT -->
