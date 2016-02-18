<? $this->load->view('shared/content_header', array('title' => 'Edit Sort', 'buttons' => array('back'))); ?>


<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$fields['module'].'/update_sort/'.$this->current_id?>" accept-charset="utf-8">

	<input type="hidden" name="fields[page_id]" value="<?=$fields['page_id']?>" />

	<div class="form_row">
		<label>Title:</label>
		<div class="field"><div class="field_text"><?=$fields['title']?></div></div>
	</div>


	<? if($fields['template_options']['child_sort_method'] != 'manually'): ?>
	
		<fieldset id="set_templates" class="open">
			<strong>
			ERROR: The template for this page has not beeen enabled for manual sorting. Current sort method is '<?=$fields['template_options']['child_sort_method']?>'.
			<br/>An administrator must make this change.
			</strong>
		</fieldset>			
		
	
	<? else: ?>
		<fieldset id="set_templates" class="open">
			<legend>Children</legend>
			<div class="fs_content" id="edit_sort_items">
	
				<? 
					$i = 0;
					foreach ($fields['children'] as $child): ?>
					
					<? $icon = ($child['type'] == 'section') ? 'folder' : 'document'; ?>
					<? $i++; ?>
					
					<div class="form_row" id="<?=$i?>_node">
							<img src="<?=$this->asset_path?>img/mini_icons/<?=$icon?>.gif" />
							<?=$child['title']?>
							<input type="hidden" name="fields[children][<?=$child['page_id']?>]" value="<?=$child['status']?>" />
					</div>
				<? endforeach; ?>
	
			</div>
		</fieldset>
		<button id="submit" name="submit" value="update_sort" class="button" type="submit"><span>SAVE SORT ORDER</span></button>
	
		<script language="javascript">
			document.observe('dom:loaded', function(event) {
				TNDR.Form.Actions.sort({
					item_id : '1'
					, item_path : 'edit_sort/items'
					});			
			});			
		</script>
	
	<? endif; ?>

</form>