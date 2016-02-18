<?

// Add things that could be empty
if (empty($fields['template_options']))			                $fields['template_options'] = array('');

if (empty($fields['template_options']['html_action']))			$fields['template_options']['html_action'] = null;
if (empty($fields['template_options']['xml_action']))			$fields['template_options']['xml_action'] = 'deny';
if (empty($fields['template_options']['child_sort_method']))	$fields['template_options']['child_sort_method'] = null;
if (empty($fields['template_options']['child_edit_style']))		$fields['template_options']['child_edit_style'] = null;

if (empty($fields['template_options']['event_show_time']))		$fields['template_options']['event_show_time'] = 'no';
if (empty($fields['template_options']['event_show_end']))		$fields['template_options']['event_show_end'] = 'no';
if (empty($fields['template_options']['page_id_path']))		    $fields['template_options']['page_id_path'] = 'no';
if (empty($fields['template_options']['show_import_id']))       $fields['template_options']['show_import_id'] = 'no';

if (empty($fields['template_options']['child_template']) || !is_array($fields['template_options']['child_template']))		$fields['template_options']['child_template'] = array('');
if (empty($fields['template_options']['child_type']))			$fields['template_options']['child_type'] = null;

if (empty($fields['template_file_name']))						$fields['template_file_name'] = null;

if (empty($fields['template_attributes']))						$fields['template_attributes'] = array();


// ------------------------------------------------------------------------

?>
	
<? $this->load->view('shared/content_header', array('title' => 'Edit Template', 'buttons' => array('delete', 'links'))); ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/update/'.$this->current_id?>">

	<input type="hidden" name="fields[<?=$this->id_field?>]" value="<?=$fields[$this->id_field]?>" />

	<? if ($fields[$this->id_field] < 0): ?>
		<input type="hidden" name="fields[template_type]" value="<?=$fields['template_type']?>" />
	<? endif; ?>
	
	<!-- Basic Fields -->
	
	<div class="form_row">
		<label>Title:</label>
		<div class="field"><input class="required" type="text" name="fields[template_title]" value="<?=$fields['template_title']?>" /></div>
	</div>

	<div class="form_row">
		<label>File Name:</label>
		<div class="field"><div class="field_text"><?=$fields['template_file_name']?></div></div>
	</div>
	
	<fieldset class="open">
		<legend>Template Definition</legend>
		<textarea name="fields[template_xml]" id="fields[template_xml]" style="height: 350px; width: 645px;" class="allow_tabs"><?=$fields['template_xml']?></textarea>
	</fieldset>


	<fieldset>
		<legend>HTML Settings</legend>

		<div class="fs_content">

			<div class="form_row">
				<label>HTML Action:</label>
				<div class="field"><?=form_dropdown('fields[template_options][html_action]', $this->CONF['options']['html_action'], $fields['template_options']['html_action'])?></div>
			</div>

			<div class="form_row" id="allow_html_yes">
				<label>HTML View:</label>
				<div class="field">
					<div class="field_buttons">
						<? $js_params = "{'destination' : 'fields[template_html_xsl_path]', 'default_path': '/cms/templates/'}"; ?>
						<button type="button" class="button button_outline_small" onClick="diskBrowser.open(<?=$js_params?>); return false;"><span>PICK</span></button>
					</div>
					<input type="text" name="fields[template_html_xsl_path]" id="fields[template_html_xsl_path]" value="<?=$fields['template_html_xsl_path']?>" />
				</div>
			</div>

			<div class="form_row" id="allow_html_no">
				<label>Redirect To (If Deny):</label>
				<div class="field">
	
					<?
						$htmlPage = !empty($fields['template_options']['html_redirect']) ? $fields['template_options']['html_redirect'] : '';
					?>
	
					<input type="hidden" name="html_redirect_trash" id="fields[template_options][html_redirect]" value="<?=$htmlPage?>" />
					<input type="hidden" name="fields[template_options][html_redirect]" id="fields[template_options][html_redirect][page_path]" value="<?=$htmlPage?>" />
	
					<div class="field_buttons">
						<button type="button" class="button button_outline_small" onClick="this.blur(); itemPicker.open({'module': 'page', 'destination': 'fields[template_options][html_redirect]'});"><span>CHOOSE</span></button>
					</div>
		
					<div class="field_text" id="fields[template_options][html_redirect]_display">
		
						<? if (!empty($htmlPage)) : ?>
							<img src="<?=$this->asset_path?>img/mini_icons/document.gif" width="10" height="10"/>
							<?=$htmlPage?>
						<? else: ?>
							404 Error Page
						<? endif; ?>
					
					</div>
				
				</div>
			</div>


		</div>

	</fieldset>

	<fieldset>
		<legend>XML Settings</legend>

		<div class="fs_content">
		
			<div class="form_row">
				<label>XML Action:</label>
				<div class="field"><?=form_dropdown('fields[template_options][xml_action]', $this->CONF['options']['xml_action'], $fields['template_options']['xml_action'])?></div>
			</div>
	
			<div id="options_xml_sub">
	
				<div class="form_row" id="fields[template_options][xml_xsl_path]_node">
					<label>XML View: (Optional)</label>
					<div class="field">
						<div class="field_buttons">
							<? $js_params = "{'destination' : 'fields[template_xml_xsl_path]', 'default_path': '/cms/templates/'}"; ?>
							<button type="button" class="button button_outline_small" onClick="diskBrowser.open(<?=$js_params?>); return false;"><span>PICK</span></button>
						</div>
						<input type="text" id="fields[template_xml_xsl_path]" name="fields[template_xml_xsl_path]" value="<?=$fields['template_xml_xsl_path']?>" />
					</div>
				</div>
	
			</div>
		
		</div>
		
	</fieldset>


	<fieldset class="open">
		<legend>Cache Settings</legend>

		<div class="fs_content">
		
			<div class="form_row last">
				<label>Cache Time:</label>
				<div class="field"><?=form_dropdown('fields[template_options][cache_time]'
					, $this->CONF['options']['cache_time']
					, (!empty($fields['template_options']['cache_time']) ? $fields['template_options']['cache_time'] : null)
					)?></div>
			</div>
		
		</div>
		
	</fieldset>


	<fieldset class="open">
		<legend>Calendar Settings</legend>

		<div class="fs_content">
	
			<div class="form_row">
				<div style="padding-bottom: 4px;">These settings only apply if you are creating a page intented to be a 'page_calendar_event'.</div>
			</div>
	
			<div class="form_row">
				<label>Show Time:</label>
				<div class="field"><?=form_dropdown('fields[template_options][event_show_time]', $this->ADMIN_CONF['optoions_yes_no'], $fields['template_options']['event_show_time'])?></div>
			</div>

			<div class="form_row">
				<label>Show End Date:</label>
				<div class="field"><?=form_dropdown('fields[template_options][event_show_end]', $this->ADMIN_CONF['optoions_yes_no'], $fields['template_options']['event_show_end'])?></div>
			</div>

		</div>
		
	</fieldset>


	<fieldset class="open">
		<legend>Child Settings</legend>

		<div class="fs_content">
	
			<div class="form_row">
				<div style="padding-bottom: 4px;">These settings only apply if you are creating a page intented to be a 'section'.</div>
			</div>
	
			<div class="form_row">
				<label>Edit Style:</label>
				<div class="field"><?=form_dropdown('fields[template_options][child_edit_style]', $this->CONF['options']['child_edit_style'], $fields['template_options']['child_edit_style'])?></div>
			</div>

			<div class="form_row">
				<label>Sort Method:</label>
				<div class="field">
					<?=form_dropdown('fields[template_options][child_sort_method]', $this->CONF['options']['child_sort_method'], $fields['template_options']['child_sort_method'])?>
					<input type="hidden" name="fields[orig_sort_method]" value="<?=$fields['template_options']['child_sort_method']?>" />
				</div>
			</div>
			
			<div class="form_row">
				<label>Restrict Child Template:</label>
				<div class="field" id="fields[template_options][child_template]_parent">
			
					<? 
					
					$i = 0;
					
					foreach ($fields['template_options']['child_template'] as $type) {
				
						$child_template_name 	= 'fields[template_options][child_template]['.$i.']';
						$i++;
						
						?>
						<div class="sub_row" id="<?=$child_template_name?>_node">
							<?=form_dropdown('fields[template_options][child_template]['.$i.']', $all_templates, $type)?>
							<div class="field_buttons">
                                <a onclick="TNDR.Form.Actions.dupeTemplate(this, 'fields[template_options][child_template]', 'sub_row'); return false;" href="#"><img width="10" height="10" alt="ADD" src="/cms/manage/assets/img/mini_icons/plus.gif"></a>
								<a onclick="TNDR.Form.Actions.remove('<?=$child_template_name?>'); return false;" href="#"><img width="10" height="10" alt="DELETE" src="/cms/manage/assets/img/mini_icons/minus.gif"></a>
							</div>
						</div>
				
					<? } ?>
				
				</div>

                <script id="fields[template_options][child_template]_template" type="text/html">
                    <div class="sub_row" id="fields[template_options][child_template][%%]_node">
                        <?=form_dropdown('fields[template_options][child_template][%%]', $all_templates)?>
                        <div class="field_buttons">
                            <a onclick="TNDR.Form.Actions.dupeTemplate(this, 'fields[template_options][child_template]', 'sub_row'); return false;" href="#"><img width="10" height="10" alt="ADD" src="/cms/manage/assets/img/mini_icons/plus.gif"></a>
                            <a onclick="TNDR.Form.Actions.remove('fields[template_options][child_template][%%]'); return false;" href="#"><img width="10" height="10" alt="DELETE" src="/cms/manage/assets/img/mini_icons/minus.gif"></a>
                        </div>
                    </div>
                </script>
            
            </div>

			<div class="form_row" id="fields[options][xml_scope]_node">
				<label>Restrict Child Type:</label>
				<? $child_types = array_merge(array(''=>'Not Selected'), $this->PAGE_CONF['page_types']);	?>
				<div class="field"><?=form_dropdown('fields[template_options][child_type]', $child_types, $fields['template_options']['child_type'])?></div>
			</div>
		
		</div>
		
	</fieldset>


	<fieldset class="open">
		<legend>Additional Page Data</legend>

		<div class="fs_content">
	
			<div class="form_row">
				<label>Add ID to path:</label>
				<div class="field"><?=form_dropdown('fields[template_options][page_id_path]', $this->ADMIN_CONF['optoions_yes_no'], $fields['template_options']['page_id_path'])?></div>
			</div>
			
			<div class="form_row">
                <label>Show ImportID Field:</label>
                <div class="field"><?=form_dropdown('fields[template_options][show_import_id]', $this->ADMIN_CONF['optoions_yes_no'], $fields['template_options']['show_import_id'])?></div>
            </div>

			<div class="form_row">
				<label>Attributes:</label>
				<div class="field" id="fields[template_attributes_new]_parent">

                    <div class="sub_row">
                        <div class="field_text note">Be very careful when changing as it could result in data loss.</div>
                        <div class="field_text note">You can only have a single attribute assigned once per template.</div>
                    </div>
                    
                    <?php if (count($fields['template_attributes'])): ?>

                        <!-- List -->
    					<? foreach ($fields['template_attributes'] as $group_id => $item) : ?>


                            <fieldset class="open sub_row" id="fields[template_attributes][<?=$group_id?>]_node">
                                <div class="fs_content">

                                    <input type="hidden" name="fields[template_attributes][<?=$group_id?>]" value="<?=$group_id?>" class="transform_filename" />
                                    <input type="hidden" name="fields[template_attributes][<?=$group_id?>][attribute_group_id]" value="<?=$group_id?>" class="transform_filename" />
                                    <input type="hidden" name="fields[template_attributes][<?=$group_id?>][title]" value="<?=$item['title']?>" class="transform_filename" />
                                    
                                    <div class="sub_row">
                                        <label><span class="sublabel">Name</span></label>
                                        <div class="sub_field"><div class="field_text"><?=$item['title']?></div></div>
                                        <div class="field_buttons"><a onclick="TNDR.Form.Actions.remove('fields[template_attributes][<?=$group_id?>]'); return false;" href="#"><img width="10" height="10" alt="DELETE" src="/cms/manage/assets/img/mini_icons/minus.gif"></a></div>
                                    </div>

                                    <div class="sub_row">
                                        <label><span class="sublabel">Multi?</span></label>
                                        <div class="sub_field"><?=form_dropdown('fields[template_attributes]['.$group_id.'][multi]', $this->ADMIN_CONF['optoions_yes_no'], $item['multi'])?></div>
                                    </div>

                                    <div class="sub_row">
                                        <label><span class="sublabel">Values Editable?</span></label>
                                        <div class="sub_field"><?=form_dropdown('fields[template_attributes]['.$group_id.'][can_add]', $this->ADMIN_CONF['optoions_yes_no'], $item['can_add'])?></div>
                                    </div>

                                </div>

                            </fieldset>

                        <? endforeach; ?>
                        


                    <?php endif; ?>

                    <!-- Add -->
                    <div class="sub_row" id="fields[template_attributes_new][]_node">
                        <?=form_dropdown('fields[template_attributes_new][0]', $all_attributegroups)?>
                        <div class="field_buttons">
                            <a onclick="TNDR.Form.Actions.dupeTemplate(this, 'fields[template_attributes_new]', 'sub_row'); return false;" href="#"><img width="10" height="10" alt="ADD" src="/cms/manage/assets/img/mini_icons/plus.gif"></a>
                            <a onclick="TNDR.Form.Actions.remove('fields[template_attributes_new][]'); return false;" href="#"><img width="10" height="10" alt="DELETE" src="/cms/manage/assets/img/mini_icons/minus.gif"></a>
                        </div>
                    </div>

				</div>

				<!-- Add Template -->
                <script id="fields[template_attributes_new]_template" type="text/html">
                    <div class="sub_row" id="fields[template_attributes_new][%%]_node">
                        <?=form_dropdown('fields[template_attributes_new][%%]', $all_attributegroups)?>
                        <div class="field_buttons">
                            <a onclick="TNDR.Form.Actions.dupeTemplate(this, 'fields[template_attributes_new]', 'sub_row'); return false;" href="#"><img width="10" height="10" alt="ADD" src="/cms/manage/assets/img/mini_icons/plus.gif"></a>
                            <a onclick="TNDR.Form.Actions.remove('fields[template_attributes_new][%%]_node'); return false;" href="#"><img width="10" height="10" alt="DELETE" src="/cms/manage/assets/img/mini_icons/minus.gif"></a>
                        </div>
                    </div>
                </script>
                
			</div>


		</div>
		
	</fieldset>

	<!-- START MODULE EDIT -->

	<button type="submit" class="button"><span><?=(($fields[$this->id_field] < 0) ? 'CREATE' : 'SAVE')?></span></button>

</form>

<? $this->load->view('shared/content_footer'); ?>

<!-- END PAGE EDIT -->
