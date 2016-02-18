<?

// TODO: Turn this back on
// Make sure that module pages get edited under the correct url
//if ($this->uri->segment(2) != $fields['module']) redirect('/admin/'.$fields['module'].'/edit/'.$this->current_id);

// ------------------------------------------------------------------------


$fields['template_options']['page_id_path'] = !empty($fields['template_options']['page_id_path']) ? $fields['template_options']['page_id_path'] : '';


?>

<? $this->load->view('page/edit/shared_header'); ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.'page_calendar/update/'.$this->current_id?>" accept-charset="utf-8">

	<? $this->load->view('page/edit/shared_statusbar'); ?>

	<div class="tab_set">
		
		<div class="tab_nav clearfix">
			<ul id="edit_tabs2">
				<li><a href="#tab_content"><span>Content</span></a></li>
			</ul>
		</div>

		<div id="tab_content" class="tab_content">

			<div class="form_row">
				<label>Title:</label>
				<div class="field"><input class="required" type="text" name="fields[title]" value="<?=$fields['title']?>" /></div>
		
				<input type="hidden" name="fields[page_id]" value="<?=$fields['page_id']?>" />
				<input type="hidden" name="fields[parent_id]" value="<?=$fields['parent_id']?>" />
				<input type="hidden" name="fields[published]" value="0" />
			
				<input type="hidden" name="fields[sort]" value="<?=$fields['sort']?>" />
				
				<input type="hidden" name="fields[parent_path]" value="<?=$fields['parent_path']?>" />
			
				<input type="hidden" name="fields[orig_file_name]" value="<?=$fields['file_name']?>" />
				<input type="hidden" name="fields[orig_title]" value="<?=$fields['title']?>" />
				<input type="hidden" name="fields[module]" value="<?=$fields['module']?>" />
				<input type="hidden" name="fields[type]" value="<?=$fields['type']?>" />
				<input type="hidden" name="fields[template_id]" id="fields[template_id]" value="<?=$fields['template_id']?>"/>
		
				<input type="hidden" name="fields[file_title]" id="fields[file_title]" value="<?=$fields['file_title']?>"/>
				<input type="hidden" name="fields[file_name]" id="fields[file_name]" value="<?=$fields['file_name']?>"/>
				<input type="hidden" name="fields[path]" id="fields[path]" value="<?=$fields['path']?>"/>

				<input type="hidden" name="fields[template_options][page_id_path]" id="fields[template_options][page_id_path]" value="<?=$fields['template_options']['page_id_path']?>"/>

			</div>
			
			<div class="form_row">
				<label>Start Date:</label>
				<div class="field"><input type="text" class="required action_calendar" name="fields[content_start_date_day]" id="fields[content_start_date_day]" readonly="readonly" title="Start Date" style="width: 100px;" value="<?=$fields['content_start_date_day']?>"/></div>
			</div>
			
			<? if (!empty($fields['template_options']['event_show_time']) && $fields['template_options']['event_show_time'] == 'yes'): ?>
				<div class="form_row">
					<label>Start Time:</label>
					<div class="field">
						<div style="float: left; width: 67px;"><?=form_dropdown('fields[content_start_date_time][0]', $this->CONF['time_hours'], $fields['content_start_date_time'][0], 'id="fields[content_start_date_time][0]" class="short"')?></div>
						<div style="float: left; width: 10px; font-weight: bold; padding-top: 4px;">:</div>
						<div style="float: left; width: 70px;"><?=form_dropdown('fields[content_start_date_time][1]', $this->CONF['time_mins'], $fields['content_start_date_time'][1], 'id="fields[content_start_date_time][1]" class="short"')?></div>
						<div style="float: left; width: 70px;"><?=form_dropdown('fields[content_start_date_time][2]', $this->CONF['time_ampm'], $fields['content_start_date_time'][2], 'id="fields[content_start_date_time][2]" class="short"')?></div>
					</div>
				</div>
			<? endif; ?>

			<? if (!empty($fields['template_options']['event_show_end']) && $fields['template_options']['event_show_end'] == 'yes'): ?>

				<div class="form_row">
					<label>End Date:</label>
					<div class="field"><input type="text" class="action_calendar" name="fields[content_end_date_day]" id="fields[content_end_date_day]" readonly="readonly" title="End Date" style="width: 100px;" value="<?=$fields['content_end_date_day']?>"/></div>
				</div>
	
				<? if (!empty($fields['template_options']['event_show_time']) && $fields['template_options']['event_show_time'] == 'yes'): ?>
					<div class="form_row">
						<label>End Time:</label>
						<div class="field">
							<div style="float: left; width: 67px;"><?=form_dropdown('fields[content_end_date_time][0]', $this->CONF['time_hours'], $fields['content_end_date_time'][0], 'id="fields[content_end_date_time][0]" class="short"')?></div>
							<div style="float: left; width: 10px; font-weight: bold; padding-top: 4px;">:</div>
							<div style="float: left; width: 70px;"><?=form_dropdown('fields[content_end_date_time][1]', $this->CONF['time_mins'], $fields['content_end_date_time'][1], 'id="fields[content_end_date_time][1]" class="short"')?></div>
							<div style="float: left; width: 70px;"><?=form_dropdown('fields[content_end_date_time][2]', $this->CONF['time_ampm'], $fields['content_end_date_time'][2], 'id="fields[content_end_date_time][2]" class="short"')?></div>
						</div>
					</div>
				<? endif; ?>

			<? endif; ?>

			<div class="form_row">
				<label>Full Path:</label>
				<div class="field_text" id="path_display"><?=$fields['path']?></div>
			</div>
			
			<? if ($this->authentication->hasPermission('page_options')): ?>
		
				<? $class = (!empty($fields['template_id'])) ? 'closed' : 'open'; ?>
		
				<!-- Template Options -->
				<div class="form_row">
					<label>Template:</label>
					<div class="field">
		
						<div class="field_buttons">
							<button type="button" class="button button_outline_small" onClick="this.blur(); itemPicker.open({'module':'template', 'destination': 'fields[template_id]', 'submitForm': 'submit_draft', 'type': '|template|'});"><span>CHOOSE</span></button>
						</div>
			
						<div class="field_text" id="fields[template_id]_display">
			
							<? if ($fields['template_id'] > 0) {
								
									echo '<img src="'.$this->asset_path.'img/mini_icons/copy.gif" width="10" height="10"/>';
									echo ' <a href="'.$this->admin_path.'template/edit/'.$fields['template_id'].'?return_url=/'.$this->admin_dir.'page/edit/'.$this->current_id.'">'.$fields['template_title'].'</a>';
		
								} else {
									echo '(No Template)';
								}
													
							?>
						
						</div>
		
					</div>
				</div>
		
			<? endif; ?>

            <? $this->load->view('page/edit/page_attributes'); ?>
		
			<? if (!empty($fields['template_id'])): ?>
				<!-- CONTENT -->
				<?=$this->xsl_transform->transform('/application/xsl/template_nodes/edit_page.xsl', array('fields'=>$fields));?>
			<? endif; ?>
	
		</div> <!-- #tab_content -->
		
		<!-- META INFORMATION -->
		<? //$this->load->view('page/edit/shared_tab_meta_info'); ?>
		

	</div> <!-- #tab_set -->
			
	<div id="edit_form_bottom">

		<? $this->load->view('page/edit/shared_publish_controls'); ?>
	
		<? $this->load->view('page/edit/shared_buttons'); ?>
	
	</div>

	<script language="javascript">
		new Control.Tabs('edit_tabs2', {
			afterChange: function(new_container){  
				refreshCLE();
			}		
		});
	</script>

</form>

<? $this->load->view('page/edit/shared_footer'); ?>

<!-- END PAGE EDIT -->