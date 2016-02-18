<?php

$fields['tracking_js']['misc'] = !empty($fields['tracking_js']['misc']) ? $fields['tracking_js']['misc'] : '';
$fields['template_options']['page_id_path'] = !empty($fields['template_options']['page_id_path']) ? $fields['template_options']['page_id_path'] : '';

?>

	<!-- Basic Fields -->

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
		<input type="hidden" name="fields[template_id]" id="fields[template_id]" value="<?=$fields['template_id']?>"/>

		<input type="hidden" name="fields[template_options][page_id_path]" id="fields[template_options][page_id_path]" value="<?=$fields['template_options']['page_id_path']?>"/>

	</div>


	<? if ($fields['type'] == 'secure_page'):
	
		if (empty($fields['options']['secure_password'])) $fields['options']['secure_password'] = null;
		?>	


		<div class="form_row">
			<label>Username:</label>
			<div class="field"><input class="required" type="text" id="fields[file_title]" name="fields[file_title]" value="<?=$fields['file_title']?>" /></div>
		</div>

		<div class="form_row">
			<label>Password:</label>
			<div class="field"><input class="required" type="password" name="fields[options][secure_password]" id="fields[options][secure_password]" value="<?=$fields['options']['secure_password']?>" /></div>
			<script language="javascript">
				function showPW(field) {
					if ((f = $(field))) f.setAttribute('type', 'text');
				}
			</script>
			<div class="button">(<a href="#SHOW" onClick="showPW('fields[options][secure_password]'); return false;">Show Password</a>)</div>
		</div>

		<div class="form_row">
			<label>Full Path:</label>
			<div class="field_text" id="path_display"><?=$fields['path']?></div>
			<? $js_params = '{id: \''.$this->current_id.'\', parentPath : \''.$fields['parent_path'].'\', messageField: \'file_name_error\'}'; ?>
			<script language="javascript">
				document.observe('dom:loaded', function() {
					PathChecker.initialize('fields[file_title]','path_display', <?=$js_params?>);
				});
			</script>
		</div>

	<? elseif ($fields['type'] != 'root') : ?>

		<div class="form_row">
			<label>File Name:</label>
			<div class="field"><input class="required" type="text" id="fields[file_title]" name="fields[file_title]" value="<?=$fields['file_title']?>" /></div>
		</div>

		<div class="form_row">
			<label>Full Path:</label>
			<div class="field_text" id="path_display"><?=$fields['path']?></div>
			<? $js_params = '{id: \''.$this->current_id.'\', parentPath : \''.$fields['parent_path'].'\', messageField: \'file_name_error\'}'; ?>
			<script language="javascript">
				document.observe('dom:loaded', function() {
					PathChecker.initialize('fields[file_title]','path_display', <?=$js_params?>);
				});
			</script>
		</div>
	
	<? else: ?>

		<div class="form_row">
			<label>File Name:</label>
			<div class="field"><div class="field_text">/</div>	</div>
		</div>

	<? endif; ?>

	<? if ($this->authentication->hasPermission('page_administration')): ?>
		<div class="form_row" id="fields[options][xml_scope]_node">
			<label>Page Type:</label>
			<div class="field"><?=form_dropdown('fields[type]', $this->CONF['page_types'], $fields['type'])?></div>
		</div>
	<? else: ?>
		<input type="hidden" name="fields[type]" value="<?=$fields['type']?>" />
	<? endif; ?>

	<div class="form_row">
		<label>Include in sitemap:</label>
		<div class="field"><?=form_dropdown('fields[options][include_sitemap]', $this->ADMIN_CONF['optoions_yes_no'], $fields['options']['include_sitemap'])?></div>
	</div>

    <div class="form_row">
        <label>Meta Title</label>
        <div class="field"><input type="text" name="fields[meta_title]" title="Meta Title" value="<?=$fields['meta_title']?>"/></div>
        <div class="note"><strong>Optional</strong> This is to override the automated title generation.</div>
    </div>
    
    <div class="form_row">
        <label>Meta Description</label>
        <div class="field">
            <textarea name="fields[meta_description]" title="Meta Description" style="height: 39px;"><?=$fields['meta_description']?></textarea>
        </div>
    </div>

    <div class="form_row">
        <label>Meta Image</label>
        <div class="field"><input type="text" name="fields[meta_image]" id="fields[meta_image][file_path]" title="Meta Title" value="<?=$fields['meta_image']?>"/></div>
		<div class="field_buttons">
			<button onclick="itemPicker.open({module: 'file', current: '<?=$fields['meta_image']?>', destination: 'fields[meta_image]'}); return false;" class="button button_outline_small" type="button"><span>PICK</span></button>
		</div>
        <input type="hidden" name="fields_meta_image" id="fields[meta_image]" />
    </div>
    








































    

    <? if (!empty($fields['template_options']['show_import_id']) && ($fields['template_options']['show_import_id'] == 'yes')): ?>

        <div class="form_row">
            <label>Import Id:</label>
            <div class="field"><input type="text" id="fields[import_id]" name="fields[import_id]" value="<?=$fields['import_id']?>" /></div>
        </div>

    <? endif; ?>
    
    <? if (!empty(CI()->CONF['omniture_keys']) && count(CI()->CONF['omniture_keys'])): ?>

        <div class="form_row">
            <label>Omniture Tracking JS</label>
            <div class="field"><div class="field_text" style="font-style: oblique;"><strong>Optional</strong> Values here will override the automatic assignments.</div></div>
        
            <?php 
            
                foreach (CI()->CONF['omniture_keys'] as $key) {
            
                    $value = !empty($fields['tracking_js']['omniture'][$key]) ? $fields['tracking_js']['omniture'][$key] : '';
                
                    echo '<div class="sub_row">'
                        .'<label><span class="sublabel">'.$key.'</span></label>'
                        .'<div class="sub_field"><input type="text" name="fields[tracking_js][omniture]['.$key.']" value="'.$value.'" /></div>'
                        .'</div>';
                
                }
            
            ?>
        
        </div>
    
    <? endif; ?>

    <div class="form_row">
        <label>Misc Tracking JS</label>
        <div class="field">
            <textarea name="fields[tracking_js][misc]"><?=trim($fields['tracking_js']['misc'])?></textarea>
        </div>
    </div>
