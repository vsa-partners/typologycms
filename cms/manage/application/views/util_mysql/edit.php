<? $this->load->view('shared/content_header', array('title' => 'DB : TABLE : '.$this->current_table.' : '.$this->current_id));  ?>

<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module_path.'/'.$this->current_table.'/edit/'.$this->current_id?>" accept-charset="utf-8">
	<? foreach ($fields as $field) {
	
		$value = (!empty($item[$field->name])) ? $item[$field->name] : '';
		
		if (is_array($value)) $value = json_encode($value);
		
		echo '<div class="form_row">'
			.'<label>'.$field->name.':</label>'
			.'<div class="field">';
			
		if ($field->primary_key == 1) {
			echo '<div class="field_text">'.$value.'</div>';
			echo '<input type="hidden" name="fields['.$field->name.']" value="'.$value.'" />';
		} else if ($field->type == 'blob') {
			echo '<textarea name="fields['.$field->name.']">'.$value.'</textarea>';
		} else {
			echo '<input type="text" name="fields['.$field->name.']" value="'.$value.'" />';
		}
		
		echo '</div></div>';
	
	}
	?>

	<button id="submit" name="submit" value="submit" class="button" type="submit"><span>SUBMIT</span></button>

	<button id="delete" name="delete" value="delete" class="button button_outline" type="button" onclick="confirmDelete('<?=$this->admin_path.$this->module_path.'/'.$this->current_table.'/delete/'.$this->current_id?>'); this.blur();"><span>DELETE</span></button>

</form>