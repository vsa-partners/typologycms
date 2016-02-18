<?

// TODO: Turn this back on
// Make sure that module pages get edited under the correct url
//if ($this->uri->segment(2) != $fields['module']) redirect('/admin/'.$fields['module'].'/edit/'.$this->current_id);

// ------------------------------------------------------------------------


?>

<? $this->load->view('page/edit/shared_header'); ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$fields['module'].'/update/'.$this->current_id?>" accept-charset="utf-8">

	<? $this->load->view('page/edit/shared_statusbar'); ?>

	<div class="tab_set">
		
		<div class="tab_nav clearfix">
			<ul id="edit_tabs2">
				<? if (!empty($fields['template_options']['child_edit_style']) && ($fields['template_options']['child_edit_style'] == 'list')) echo '<li><a href="#tab_children"><span>Children</span></a></li>'; ?>
				<li><a href="#tab_content"><span>Mirror</span></a></li>
				<li><a href="#tab_information"><span>Page Information</span></a></li>
			</ul>
		</div>

		<!-- BASIC INFORMATION -->
		<? $this->load->view('page/edit/shared_tab_basic_info'); ?>

		<? if (!empty($fields['template_options']['child_edit_style']) && ($fields['template_options']['child_edit_style'] == 'list')) {
			$this->load->view('page/edit/shared_tab_children');
		} ?>

		<div id="tab_content" class="tab_content">
			<? $this->load->view('page/edit/shared_mirror_source'); ?>
		</div>
		
	</div> <!-- End .tab_set -->

	<div id="edit_form_bottom">

		<? $this->load->view('page/edit/shared_publish_controls'); ?>
	
		<? $this->load->view('page/edit/shared_buttons'); ?>
	
	</div>

	<script language="javascript">
		new Control.Tabs('edit_tabs2');
	</script>
	
</form>

<? $this->load->view('page/edit/shared_footer'); ?>

<!-- END PAGE EDIT -->