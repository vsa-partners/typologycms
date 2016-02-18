	<? if (($fields['type'] == 'section') && $this->authentication->hasPermission('page_options')): ?>

		<!-- Section Options -->

		<div class="form_row">
			<label>Default Page:</label>
			<div class="field">

				<span class="field_text">
				
					<? if (!empty($fields['template_options']['html_action']) && $fields['template_options']['html_action'] == 'specified') : ?>

						<?
						
							if (!empty($fields['options']['section_pages']['default_page'])) {
								$default_page 		= $fields['options']['section_pages']['default_page'];
								$default_page_item	= $this->page_model->doAutoJoin(FALSE)->first()->getById($default_page, 'navigation');
							} else {
								$default_page 		= '';
								$default_page_item	= null;
							}
								
						?>
	
						<input type="hidden" name="fields[options][section_pages][default_page]" id="fields[options][section_pages][default_page]" value="<?=$default_page?>" />
	
						<button type="button" class="button button_outline_small" onClick="this.blur(); itemPicker.open({'destination': 'fields[options][section_pages][default_page]', 'type': '|page|'});"><span>CHOOSE</span></button>
						
						<? if (!is_null($default_page_item)) : ?>
							<img src="<?=$this->asset_path?>img/mini_icons/document.gif" width="10" height="10"/>
							<?=$default_page_item['title']?>
						<? else: ?>
							(No Page)
						<? endif; ?>

					<? else: ?>
						(Not enabled in template options)
					<? endif; ?>

				</span>
			
			</div>
		</div>
			

	<? endif; ?>