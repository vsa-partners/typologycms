<? $this->load->view('shared/content_header', array('title' => 'Edit Collection : '.$fields['title'], 'buttons' => array('delete'))); ?>

<div class="tab_set">
	<div class="tab_nav clearfix">
		<ul id="edit_tabs">
			<? if(isset($files)): ?><li><a href="#tab_files"><span>Files</span></a></li><? endif; ?>
			<li><a href="#tab_props"><span>Properties</span></a></li>
		</ul>
	</div>

	<? if(isset($files)): ?>
		<div class="tab_content" id="tab_files">
			<? $this->load->view('file/list_collection_files'); ?>
		</div>
	<? endif; ?>

	<div class="tab_content" id="tab_props">

		<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/update/collection/'.$this->current_id?>" accept-charset="utf-8">

			<input type="hidden" name="fields[<?=$this->id_field?>]" value="<?=$fields[$this->id_field]?>" />
			<input type="hidden" name="fields[parent_id]" value="<?=$fields['parent_id']?>" />
			<input type="hidden" name="fields[type]" value="<?=$fields['type']?>" />


			<div class="form_row">
				<label><?=ucwords($fields['type'])?> Title:</label>
				<div class="field"><input class="required" type="text" name="fields[title]" value="<?=$fields['title']?>" /></div>
			</div>

			<? if ($fields[$this->id_field] > 0): ?>
				<div class="form_row">
					<label>Options:</label>
					<div class="field"><?pr($fields['options'])?></div>
				</div>
			<? endif; ?>

			<button type="submit" class="button"><span><?=(($fields[$this->id_field] < 0) ? 'CREATE' : 'SAVE')?></span></button>

		</form>

		<div class="clear"> </div>
	
	</div>

</div>

<script>
	new Control.Tabs('edit_tabs');
</script>


<? $this->load->view('shared/content_footer'); ?>
