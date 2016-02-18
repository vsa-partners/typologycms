<!-- START PAGE EDIT -->
<div class="content_header">
	<? 
	
		// Edit
		if (!empty($buttons) && in_array('back', $buttons) && ($this->current_id > 0)) {
		
			$back_url = $this->admin_path.$this->module.'/edit/'. $this->current_id;
					 
			echo '<button type="button" class="button button_outline" href="'.$back_url.'" onClick="document.location = this.getAttribute(\'href\'); this.blur();"><span>BACK TO EDIT</span></button>';
			
		}
		
		// Delete
		if (!empty($buttons) && in_array('delete', $buttons) && ($fields[$this->id_field] > 0)) {
		
			$del_url = $this->admin_path.$this->module.'/delete/'. $this->current_id
					 . '?'.$this->id_field.'='.$this->current_id
					 . '&DELETE=DELETE';
					 
			echo '<button type="button" class="button button_outline" onClick="confirmDelete(\''.$del_url.'\'); this.blur();"><span>DELETE</span></button>';
			
		}

		// Duplicate
		if (!empty($buttons) && in_array('dupe', $buttons) && ($fields[$this->id_field] > 0)) {

			$dupe_url = $this->ADMIN_CONF['url']['admin'].$this->module.'/edit/-1'
					 . '?parent_id='.$fields['parent_id']
					 . '&type='.$fields['type']
					 . '&template_id='.$fields['template_id'];
			
			echo '<button type="button" class="button button_outline" href="'.$dupe_url.'" onClick="document.location = this.getAttribute(\'href\'); this.blur();"><span>COPY</span></button>';
		}


		// Move
		if (!empty($buttons) && in_array('move', $buttons) && ($fields[$this->id_field] > 0)) {
			
			echo '<form action="'.$this->admin_path.$this->module.'/move/'.$this->current_id.'" id="moveForm" onSubmit="return confirmDelete(this);" class="hide">'
				. NL . '<input type="hidden" name="move_'.$this->id_field.'" value="'.$this->current_id.'"/>'
				. NL . '<input type="hidden" name="move_parent_id" id="move_parent_id"/>'
				. NL . '</form>'
				. NL . '<button type="button" class="button button_outline" onClick="itemPicker.open({\'destination\': \'move_parent_id\', \'submitForm\': \'moveForm\', \'sType\': \'|section|root|\'}); this.blur();"><span>MOVE</span></button>';

		}

		// Links
		if (!empty($buttons) && in_array('links', $buttons) && ($fields[$this->id_field] > 0)) {

			$links_url = $this->admin_path.$this->module.'/links/'.$fields[$this->id_field];
			
			echo '<button type="button" class="button button_outline" href="'.$links_url.'" onClick="document.location = this.getAttribute(\'href\'); this.blur();"><span>LINKED PAGES</span></button>';
		
		}
	?>
	
	<h2><?=(!empty($title)) ? $title  : 'Edit' ?></h2>
	
	<div class="clear"> </div>
	
</div>
