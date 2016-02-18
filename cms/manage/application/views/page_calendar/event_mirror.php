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
				<label>Full Path:</label>
				<div class="field_text" id="path_display"><?=$fields['path']?></div>
			</div>
			
			<? $this->load->view('page/edit/shared_mirror_source'); ?>
	
		</div> <!-- #tab_content -->
		

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