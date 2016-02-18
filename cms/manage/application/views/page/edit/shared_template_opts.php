	<? if ($this->authentication->hasPermission('page_options') && (!in_array($fields['type'], array('mirror_page', 'mirror_section')))): ?>


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

	<? else: ?>

		<div class="form_row">
			<label>Template:</label>
			<div class="field">
				<div class="field_text" id="fields[template_id]_display"><?=$fields['template_title']?></div>
			</div>
		</div>

	<? endif; ?>