<?

// Make sure that module pages get edited under the correct url
if (($fields['module'] == 'page_calendar') && ($this->uri->segment(2) != $fields['module']))  redirect('/'.$fields['module'].'/edit/'.$this->current_id);

// ------------------------------------------------------------------------


?>

<? $this->load->view('page/edit/shared_header'); ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$fields['module'].'/update/'.$this->current_id?>" accept-charset="utf-8">

	<? $this->load->view('page/edit/shared_statusbar'); ?>

	<div class="tab_set">
		
		<div class="tab_nav clearfix">
			<ul id="edit_tabs2">
				
				<? if (!empty($fields['template_options']['child_edit_style']) && ($fields['template_options']['child_edit_style'] == 'list')) echo '<li><a href="#tab_children"><span>Children</span></a></li>'; ?>

				<? if (($fields['type'] == 'page_database')) echo '<li><a href="#tab_children"><span>Records</span></a></li>'; ?>

				<? if (($fields['type'] == 'static') || (!empty($fields['template_id']))) echo '<li><a href="#tab_content"><span>Content</span></a></li>'; ?>
			
				<li><a href="#tab_information"><span>Page Information</span></a></li>
				<li><a href="#tab_versions"><span>History</span></a></li>
			</ul>
		</div>

		<? if (!empty($fields['template_options']['child_edit_style']) && ($fields['template_options']['child_edit_style'] == 'list')) {
			$this->load->view('page/edit/shared_tab_children');
		} ?>

		<? if (($fields['type'] == 'page_database')) {
			$this->load->view('page/edit/database_list');
		} ?>

		<? if ($fields['type'] == 'static'): ?>

			<!-- STATIC CONTENT -->
			<div id="tab_content" class="tab_content">

				<div class="form_row">
					<label>Wrapper:</label>
					<div class="field"></div>
				</div>
			
				<div class="form_row">
					<label>Page Data:</label>
					<div class="field"><textarea class="transform_noresize" name="fields[data]" style="width: 500px; height: 300px; font-family: courier;"><?=htmlentities($fields['content'], ENT_NOQUOTES, 'UTF-8');?></textarea></div>
				</div

			</div>
			
		<? elseif (($fields['type'] == 'static') || (!empty($fields['template_id']))): ?>

			<!-- CONTENT -->

			<div id="tab_content" class="tab_content">
			
			    <? $this->load->view('page/edit/page_attributes'); ?>

				<?=$this->xsl_transform->transform('/application/xsl/template_nodes/edit_page.xsl', array('fields'=>$fields));?>

			</div>
		
		<? endif; ?>


		<!-- BASIC INFORMATION -->
		<? $this->load->view('page/edit/shared_tab_basic_info'); ?>

		<!-- VERSIONS -->
		<div id="tab_versions" class="tab_content">
			<img src="<?=$this->asset_path?>img/processing.gif"/>
		</div>
	
	</div> <!-- End .tab_set -->

	<div id="edit_form_bottom">

		<? $this->load->view('page/edit/shared_publish_controls'); ?>
	
		<? $this->load->view('page/edit/shared_buttons'); ?>
	
	</div>
	
	<script language="javascript">
		
		new Control.Tabs('edit_tabs2', {
			afterChange: function(new_container){  
				
				refreshCLE();

				if (new_container.id == 'tab_events') {

					$('edit_form_bottom').hide();
				
				} else if (new_container.id == 'tab_versions') {
				
					$('edit_form_bottom').hide();
					
					if ($('tab_versions').getAttribute('loaded') != 'true') {

						new Ajax.Updater('tab_versions', ADMIN_PATH+'page/versions/<?=$this->current_id?>', {
							parameters: {page_id: <?=$this->current_id?>}
							, onSuccess: function(transport) {
								$(transport.request.container.success).setAttribute('loaded', 'true');
							}
						});
					
					}
					
				} else {
					$('edit_form_bottom').show();
				}
			}		
		});
	</script>
	
</form>

<? $this->load->view('page/edit/shared_footer'); ?>

<!-- END PAGE EDIT -->