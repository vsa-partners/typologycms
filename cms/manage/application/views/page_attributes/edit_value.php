<div class="content_header">

    <button onclick="confirmDelete('<?=$this->admin_path.$this->module?>/delete_value/<?=$fields['page_attributevalue_id']?>?page_attributevalue_id=<?=$fields['page_attributevalue_id']?>&amp;DELETE=DELETE'); this.blur();" class="button button_outline" type="button"><span>DELETE</span></button>   

    <h2>Edit Value</h2>
    <div class="clear"> </div>
   
</div>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/update_value/'.$fields['page_attributevalue_id']?>">

	<input type="hidden" name="fields[page_attributevalue_id]" value="<?=$fields['page_attributevalue_id']?>" />

    <div class="form_row">
        <label>Key:</label>

        <? if ($this->authentication->hasPermission('page_administration')): ?>
            <div class="field"><input class="required" type="text" name="fields[value_key]" value="<?=$fields['value_key']?>" /></div>
        <? else: ?>
            <div class="field_text"><?=$fields['value_key']?></div>
        <? endif; ?>

    </div>


	<div class="form_row">
		<label>Title:</label>
		<div class="field"><input class="required" type="text" name="fields[value_title]" value="<?=$fields['value_title']?>" /></div>
	</div>


    <div class="form_row">
        <label>Short Title:</label>
        <div class="field"><input type="text" name="fields[value_short_title]" value="<?=$fields['value_short_title']?>" /></div>
    </div>

    <div class="form_row">
        <label>Text:</label>
        <div class="field"><textarea name="fields[value_text]"><?=$fields['value_text']?></textarea></div>
    </div>

    <div class="form_row">
        <label>Image:</label>

        <div class="field">
            <div class="field_buttons">
                <div style="display: none; padding: 5px 5px 0 0; float: left;" class="file_field_buttons">
                    <a onclick="itemPicker.clear('fields[content][data][slide][0][image][0]', ['file_path', 'file_title']); return false;" class="button_clear" href="#"><img width="10" height="10" src="<?$this->asset_path?>img/mini_icons/cross.gif"></a>
                </div>                
                <button onclick="itemPicker.open({module: 'file', current: '<?=$fields['value_image']?>', destination: 'fields[value_image]'}); return false;" class="button button_outline_small" type="button"><span>PICK</span></button>
            </div>
            <input type="text" id="fields[value_image][file_path]" name="fields[value_image]" readonly="" value="<?=$fields['value_image']?>" />
        </div>

        
        <input type="hidden" value="" id="fields[value_image]" name="fields[value_image_junk]" readonly="" />

    </div>

	<!-- START MODULE EDIT -->

	<button type="submit" class="button"><span><?=(($fields[$this->id_field] < 0) ? 'CREATE' : 'SAVE')?></span></button>

</form>

<? $this->load->view('shared/content_footer'); ?>

<!-- END PAGE EDIT -->
