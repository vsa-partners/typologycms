<?

	if (empty($fields['children'])) $fields['children'] = array();

?>
<? $this->load->view('page/edit/shared_header'); ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$fields['module'].'/update/'.$this->current_id?>" accept-charset="utf-8">

	<? $this->load->view('page/edit/shared_statusbar'); ?>

	<div class="tab_set">
		
		<div class="tab_nav clearfix">
			<ul id="edit_tabs2">
				<li><a href="#tab_events"><span>Pages</span></a></li>
				<? if ((!empty($fields['template_id']))) echo '<li><a href="#tab_content"><span>Content</span></a></li>'; ?>
				<li><a href="#tab_information"><span>Page Information</span></a></li>
			</ul>
		</div>

		<div id="tab_events" class="tab_content">
			
			<button type="button" class="button button_small" onclick="document.location = this.getAttribute('href'); this.blur();" href="<?=$this->admin_path.'page_calendar/create/'.$fields[$this->id_field]?>"><span>ADD PAGE</span></button>
			<br/><br/>					
			
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
				<tr>
					<th>&nbsp;</th>
					<th>Date</th>
					<th>Title</th>
					<th width="10">&nbsp;</th>
				</tr>
				
				<? foreach ($fields['children'] as $row):
					$icon = '<img src="' . CI()->asset_path . $this->CONF['items'][$row['type']]['icons']['sm'] . '" width="10" height="10"/> ';
					?>
			
					<tr id="item_<?=$row[$this->id_field]?>">
						<td width="10"><?=$icon?></td>
						<td width="150"><?=date(DATE_DAY_DISPLAY_FORMAT, strtotime($row['content_start_date']))?></td>
						<td><a href="<?=$this->admin_path.'page_calendar/event/'.$row[$this->id_field]?>"><?=$row['title']?></a></td>
						<td width="10"><div class="status status_<?=$row['status']?>"><?=$row['status']?></div></td>
					</tr>

				<? endforeach; ?>

				<? if (!count($fields['children'])) : ?>
					<tr><td colspan="30">(No Events)</td></tr>
				<? endif; ?>
				
			</table>

		</div>


		<? if (!empty($fields['template_id'])): ?>

			<!-- CONTENT -->

			<div id="tab_content" class="tab_content">
				<?=$this->xsl_transform->transform('/application/xsl/template_nodes/edit_page.xsl', array('fields'=>$fields));?>
			</div>
		
		<? endif; ?>


		<!-- BASIC INFORMATION -->
		<? $this->load->view('page/edit/shared_tab_basic_info'); ?>

	</div> <!-- End .tab_set -->

	<div id="edit_form_bottom">

		<? $this->load->view('page/edit/shared_publish_controls'); ?>
	
		<? $this->load->view('page/edit/shared_buttons'); ?>
	
	</div>
	
	<script language="javascript">		
		new Control.Tabs('edit_tabs2', {
			afterChange: function(new_container){  
				if (new_container.id == 'tab_events') {
					$('edit_form_bottom').hide();
				} else {
					$('edit_form_bottom').show();
				}
			}		
		});
	</script>
	
</form>

<? $this->load->view('page/edit/shared_footer'); ?>

