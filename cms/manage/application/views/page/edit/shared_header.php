<!-- START PAGE EDIT -->
<div class="content_header" class="clearfix">

		<div id="dropdown_1" class="dropdown_button right">
			<a class="clearfix button" onclick="toggleDropdown(1); return false;" href="#"><span>Page Actions</span></a>
			
			<ul style="display: none;" class="links">
		
				<? 
		
				// Delete
				if (($fields[$this->id_field] > 0) && $this->authentication->hasPermission('global_delete')  &&  (empty($buttons) || (!empty($buttons) && in_array('delete', $buttons)))) {
				
					$del_url = $this->admin_path.$this->module.'/delete/'. $this->current_id
							 . '?'.$this->id_field.'='.$this->current_id
							 . '&DELETE=DELETE';
							 
					echo '<li><a href="#" onClick="confirmDelete(\''.$del_url.'\'); this.blur();">DELETE</a></li>';

					if ($fields['publish_date'] != EMPTY_DATE) {

						$del_url = $this->admin_path.$this->module.'/unpublish/'. $this->current_id
								 . '?'.$this->id_field.'='.$this->current_id
								 . '&DELETE=DELETE';
								 
						echo '<li><a href="#" onClick="confirmDelete(\''.$del_url.'\', \'Are you sure you want remove this from the live website and delete any outstanding pubish queues?\'); this.blur();">UNPUBLISH</a></li>';
					
					}
					
				}

				// Move
				if ((empty($buttons) || (!empty($buttons) && in_array('move', $buttons))) && ($fields[$this->id_field] > 0)) {

					$js_params = array(
						'destination'		=> 'move_parent_id'
						, 'submitForm'		=> 'move_submit'
						, 'select_type'		=> '|section|root|'
						);
					
					echo '<li>'
						. '<form action="'.$this->admin_path.$this->module.'/move/'.$this->current_id.'" id="moveForm" class="hide">'
						. NL . '<input type="hidden" name="move_'.$this->id_field.'" value="'.$this->current_id.'"/>'
						. NL . '<input type="hidden" name="move_type" value="'.$fields['type'].'" />'
						. NL . '<input type="hidden" name="move_status" value="'.$fields['status'].'" />'
						. NL . '<input type="hidden" name="move_parent_id" id="move_parent_id"/>'
						. NL . '<input type="submit" name="submit" id="move_submit"/>'
						. NL . '</form>'
						. NL . '<a href="#" onClick=\'itemPicker.open('.json_encode($js_params).'); this.blur();\'>MOVE</a>'
						. '</li>';
				}


				// Duplicate
				if (($fields[$this->id_field] > 0) && $this->authentication->hasPermission('global_create') && (empty($buttons) || (!empty($buttons) && in_array('dupe', $buttons)))) {
		
					$dupe_url = $this->admin_path.$this->module.'/create/'.$fields['parent_id']
							 . '?type='.$fields['type']
							 . '&template_id='.$fields['template_id'];
					
					echo '<li><a href="'.$dupe_url.'" onClick="this.blur();">COPY</a></li>';
					
					/*
					$dupe_url = $this->admin_path.$this->module.'/create/'.$fields['parent_id']
							 . '?type='.$fields['type']
							 . '&template_id='.$fields['template_id']
							 . '&content=true';
					
					echo '<li><a href="'.$dupe_url.'" onClick="this.blur();">COPY w/ CONTENT</a></li>';
					*/

				}
		
		
				// Links
				if ((empty($buttons) || (!empty($buttons) && in_array('create', $buttons))) && ($fields[$this->id_field] > 0)) {
						
					if ($fields['type'] == 'section') {
		
						$links_url = $this->admin_path.$this->module.'/create/'.$fields[$this->id_field];
						echo '<li><a href="'.$links_url.'" onClick="this.blur();">ADD CHILD</a></li>';
						
					}
		
				}

				// Raw XML
				if ((empty($buttons) || (!empty($buttons) && in_array('edit_xml', $buttons))) && ($this->authentication->hasPermission('page_edit_xml'))) {
						
					$links_url = $this->admin_path.$this->module.'/edit/'.$fields[$this->id_field] . '/xml/';
					echo '<li><a href="'.$links_url.'" onClick="this.blur();">EDIT XML</a></li>';
		
				}


				// Sort
				if ((empty($buttons) || (!empty($buttons) && in_array('sort', $buttons))) && ($fields[$this->id_field] > 0) && ($fields['type'] == 'section')) {
						
					$links_url = $this->admin_path.$this->module.'/edit_sort/'.$fields[$this->id_field];
					echo '<li><a href="'.$links_url.'" onClick="this.blur();">SORT CHILDREN</a></li>';
		
				}
				?>
				<li><a href="<?=($this->admin_path.$this->module.'/activity/'.$fields[$this->id_field])?>" onClick="this.blur();">ACTIVITY LOG</a></li>
			</ul>
		</div><!-- /.dropdown_button -->

		<?
		
		// View
		if ((!empty($fields['template_options']['allow_xml']) && ($fields['template_options']['allow_xml'] == 'yes'))
			&& (empty($buttons) || (!empty($buttons) && in_array('view', $buttons))) && ($fields[$this->id_field] > 0)) {
			echo '<button class="right button button_outline" href="'.rtrim($this->ADMIN_CONF['publish']['remote_url'], '/').'/XML'.$fields['path'].'" target="_blank" onClick="document.location = this.getAttribute(\'href\'); this.blur();"><span>VIEW LIVE XML</span></button>';
		}
		if ((empty($buttons) || (!empty($buttons) && in_array('view', $buttons))) && ($fields[$this->id_field] > 0)) {
			echo '<button class="right button button_outline" href="'.rtrim($this->ADMIN_CONF['publish']['remote_url'], '/').$fields['path'].'" target="_blank" onClick="document.location = this.getAttribute(\'href\'); this.blur();"><span>VIEW LIVE PAGE</span></button>';
		}

		if ($fields['type'] == 'custom_page') {
			$links_url = $this->admin_path.$this->module.'/links/'.$fields[$this->id_field];
			echo '<button class="right button button_outline" href="'.$links_url.'" onClick="document.location = this.getAttribute(\'href\'); this.blur();"><span>CONTENT USERS</span></button>';
		}
		?>


	
	<h2>Edit Page</h2>
</div>
