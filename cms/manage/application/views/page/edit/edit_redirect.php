<?

// ------------------------------------------------------------------------

// Add things that could be empty
if (empty($fields['options']['redirect_path']))	$fields['options']['redirect_path'] = null;
?>


<? $this->load->view('page/edit/shared_header', array('buttons' => array('delete'))); ?>

<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$fields['module'].'/update/'.$this->current_id?>" accept-charset="utf-8">

	<? $this->load->view('page/edit/shared_statusbar'); ?>

	<? $this->load->view('page/edit/shared_form_top'); ?>

	<div class="form_row">
		<label>Redirect Path:</label>
		<div class="field"><input class="required" type="text" name="fields[options][redirect_path]" id="fields[options][redirect_path]" value="<?=$fields['options']['redirect_path']?>" /></div>
	</div>
		
	<? $this->load->view('page/edit/shared_buttons'); ?>

</form>

<? $this->load->view('page/edit/shared_footer'); ?>

<!-- END PAGE EDIT -->