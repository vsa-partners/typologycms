<? if (count($items)): ?>

	<? $this->load->view('shared/content_header', array('title' => 'Edit Config: ' . $edit_zone)); ?>
	
	
	<form method="post" class="tndr_form" name="editForm" id="editForm" class="edit_form" action="<?=$this->admin_path.$this->module?>/update/" accept-charset="utf-8">

		<? 

		for ($i=0; $i<count($items); $i++) {
		
			$item = $items[$i];
			
			// Group Header?
			if (($i == 0) || ($item['zone_group'] != $items[$i-1]['zone_group'])) {
			
				if (($i != 0)) {
					// Close previous form seciton
					echo '</div></div><br/>';
				}
			
				echo '<div class="form_section"><div class="form_section_header">'.strtoupper(humanize($item['zone_group'])).'</div>'
					.'<div class="section_content">';
			}

			if (empty($item['options']['input_type'])) $item['options']['input_type'] = 'text';
		
			echo '<div class="form_row">'
				.'<label>'.humanize($item['name']).'</label>';

				if (!empty($item['options']['multi']) && ($item['options']['multi'] == 'yes')) {
	
					if (!is_array($item['value'])) $item['value'] = array($item['value']);
	
					echo '<div class="field">';
	
					foreach ($item['value'] as $key => $val) {
						
						$row_id = 'fields['.$item['config_id'].']';
					
						echo '<div class="sub_row" id="'.$row_id.'_'.$key.'_node">';
	
						// Dupe Buttons
						echo '<div class="field_buttons">'
							.'<a href="#" onClick="TNDR.Form.Actions.dupeStatic(this, \'field\', \'sub_row\', \''.$row_id.'_\'); return false;"><img src="'.$this->asset_path.'img/mini_icons/plus.gif" width="10" height="10" alt="ADD" /></a>'
							.'<a href="#" onClick="TNDR.Form.Actions.remove(\''.$row_id.'_'.$key.'\'); return false;"><img src="'.$this->asset_path.'img/mini_icons/minus.gif" width="10" height="10" alt="DELETE" /></a>';

						if ($item['options']['input_type'] == 'file') {
							$js_params = "{destination : '".$row_id."_".$key."', default_path : '/'}";
							echo '<button type="button" class="button button_outline_small" onClick="diskBrowser.open('.$js_params.'); return false;"><span>PICK</span></button>';
						}
						
						echo '</div>';
						
						echo '<input type="text" id="'.$row_id.'_'.$key.'" name="'.$row_id.'[value][]" value="'.$val.'" />'
							.'</div>'
							.'<input type="hidden" id="'.$row_id.'_'.$key.'" name="'.$row_id.'[previous][]" value="'.$val.'" />';

							
					}
				
					echo '</div>' // End: .field
						.'<div class="field_buttons"></div>';
					
				} else {
				
					echo '<div class="field">';

						if ($item['options']['input_type'] == 'file') {
							$js_params = "{destination : 'fields[".$item['config_id']."]'}";
							echo '<div class="field_buttons"><button type="button" class="button button_outline_small" onClick="diskBrowser.open('.$js_params.'); return false;"><span>PICK</span></button></div>';
						}


						switch ($item['options']['input_type']){

							case 'select':
							
								$opts = explode(',', $item['options']['options']);
								
								echo '<select id="fields['.$item['config_id'].'][value]" name="fields['.$item['config_id'].'][value]">';

								foreach ($opts as $opt) {
									$selected = ($item['value'] == $opt) ? 'selected' : '';
									echo '<option '.$selected.' value="'.$opt.'">'.$opt.'</option>';
								}
								
								echo '</select>';
								echo '<input type="hidden" id="fields['.$item['config_id'].'][previous]" name="fields['.$item['config_id'].'][previous]" value="X" />';
								break;

							case 'textarea':
							
								if (is_array($item['value'])) {
									
									foreach ($item['value'] as $key => $val) {
										echo '<textarea class=""id="fields['.$item['config_id'].']['.$key.'][value]" name="fields['.$item['config_id'].'][value][]">'.$val.'</textarea>';
									}
								
								} else {								
									echo '<textarea class="wide" id="fields['.$item['config_id'].'][value]" name="fields['.$item['config_id'].'][value]">'.($item['value']).'</textarea>';
								}
								echo '<input type="hidden" id="fields['.$item['config_id'].'][previous]" name="fields['.$item['config_id'].'][previous]" value="X" />';
								break;
						
							case 'bool':
								
									$values = array('TRUE', 'FALSE');
									
									echo '<select name="fields['.$item['config_id'].'][value]">';
									foreach ($values as $value){
										$selected = ($item['value'] == $value) ? 'SELECTED' : '';
										echo '<option '.$selected.'>'.$value.'</option>';
										
									}									
									echo '</select>';
								echo '<input type="hidden" id="fields['.$item['config_id'].'][previous]" name="fields['.$item['config_id'].'][previous]" value="'.$item['value'].'" />';
								break;
								
							case 'string':
							default:
								
								if (is_array($item['value'])) {
									
									foreach ($item['value'] as $key => $val) {
										echo '<input type="text" id="fields['.$item['config_id'].']['.$key.'][value]" name="fields['.$item['config_id'].'][value][]" value="'.$val.'" />';
									}
								
								} else {								
									echo '<input type="text" id="fields['.$item['config_id'].'][value]" name="fields['.$item['config_id'].'][value]" value="'.$item['value'].'" />';
								}
								echo '<input type="hidden" id="fields['.$item['config_id'].'][previous]" name="fields['.$item['config_id'].'][previous]" value="'.$item['value'].'" />';
							
								break;
						}


					echo '</div>'; // close .field				
				
				}
			
			echo ''
				. ((!empty($item['options']['note'])) ? '<div class="note">'.$item['options']['note'].'</div>' : '')
				. '</div>'; // close form_node

		
		} // End foreach ($items)?>
		
		</div></div><br/><!-- Close form_section -->
	
		<div class="button_row">
			<input type="hidden" name="zone" value="<?=$edit_zone?>"/>
			<button type="submit" class="button"><span>SAVE</span></button>	
		</div>
		
		
	</form>

<? endif; ?>