			<div class="form_row" id="fields[options][htmlPage]_node">
				<label>Mirror Source:</label>
				<div class="field">
		
					<?
						$mirror_title 	= !empty($fields['source_title']) ? $fields['source_title'] : '';
						$mirror_source 	= !empty($fields['source_id']) ? $fields['source_id'] : '';
						$mirror_path 	= !empty($fields['source_path']) ? $fields['source_path'] : '';
					?>
		
					<input type="hidden" name="fields[source_id]" id="fields[source_id]" value="<?=$mirror_source?>" />
		
					<div class="field_buttons">
						<button type="button" class="button button_outline_small" onClick="this.blur(); itemPicker.open({'module': 'page', 'destination': 'fields[source_id]'});"><span>CHOOSE</span></button>
					</div>
		
					<div class="field_text">
						<img src="<?=$this->asset_path?>img/mini_icons/document.gif" width="10" height="10"/>
						<span id="fields[source_id]_display">
							<? if ($mirror_source > 0) : ?>
								<a href="<?=$this->admin_path.$fields['module'].'/edit/'.$mirror_source?>"><?=$mirror_title?></a>
								<div style="font-size: 9px; margin: 4px 0 0 11px; width: 250px; line-height: 10px;"><?=$mirror_path?></div>
							<? else: ?>
								(Not Defined)
							<? endif; ?>
						</span>					
					</div>	
	
				</div>
			</div>
