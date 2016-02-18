<? $this->load->view('shared/content_header', array('title' => 'Edit User', 'buttons' => array('delete'))); ?>

<? if (empty($fields['options']['receive_pending_emails'])) $fields['options']['receive_pending_emails'] = 0; ?>

<form method="post" class="tndr_form" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/update/'.$this->current_id?>">

	<input type="hidden" name="fields[<?=$this->id_field?>]" value="<?=$fields[$this->id_field]?>" />

	<!-- Basic Fields -->
	
	<div class="form_row">
		<label>Username:</label>
		<div class="field"><input class="required" type="text" name="fields[user]" value="<?=$fields['user']?>" /></div>
	</div>

	<div class="form_row">
		<label>Email:</label>
		<div class="field"><input type="text" name="fields[email]" value="<?=$fields['email']?>" /></div>
	</div>

	<? if(!empty($fields['password'])) { ?>	

		<div class="form_row" id="password_current">
			<label>Password:</label>
			<div class="field">
				<div class="field_text">(<a href="#CHANGE_PASSWORD" onclick="TNDR.Form.Actions.toggleHideStatic('password_new,password_confirm', 'show'); TNDR.Form.Actions.toggleHideStatic('password_current', 'hide'); return false;">Password set click to change</a>)</div>
			</div>
		</div>			
		<div class="form_row" id="password_new" style="display: none;">
			<label>Password New:</label>
			<div class="field">
				<input type="password" name="fields[password_new]"/>
			</div>
		</div>
		<div class="form_row" id="password_confirm" style="display: none;">
			<label>Password New: (Confirm)</label>
			<div class="field"><input class="" type="password" name="fields[password_confirm]"/></div>
			<div class="note">(<a href="#CANCEL" onclick="TNDR.Form.Actions.toggleHideStatic('password_new,password_confirm', 'hide'); TNDR.Form.Actions.toggleHideStatic('password_current', 'show'); return false;" style="font-style: none;">Cancel</a>)</div>
		</div>

	<? } else { ?>

		<!-- New user -->
		
		<div class="form_row">
			<label>Password:</label>
			<div class="field"><input class="required" type="password" name="fields[password_new]"/></div>
		</div>
		<div class="form_row">
			<label>Password: (Confirm)</label>
			<div class="field"><input class="required validate_confirm" confirm_with="fields[password_new]" type="password" name="fields[password_confirm]"/></div>
		</div>

	<? } ?>		

	<div class="form_row">
		<label>Enabled:</label>
		<div class="field"><?=form_dropdown('fields[enabled]', $this->CONF['options']['enabled'], $fields['enabled'])?></div>
	</div>	

	<div class="form_row">
		<label>User Group:</label>
		<div class="field radio_set">
			<? 
				if (($fields['permission_group'] == 'administrator') && ($this->authentication->get('permission_group') != 'administrator')) {
				
					echo '<div class="field_text">Administrator</div>';
				
				} else {
			
					$first = TRUE;
					foreach ($this->ADMIN_CONF['user_groups'] as $key => $group) {
					
						$disabled 	= (($key == 'administrator') && $this->authentication->get('permission_group') != 'administrator') ? TRUE : FALSE;
						$selected 	= ((!empty($fields['permission_group']) && ($fields['permission_group'] == $key)) || (empty($fields['permission_group']) && $first)) ? TRUE : FALSE;
						//$params		= 'class="toggle_hide" tohide="options_draft_email" showif="content_publisher,site_supervisor,administrator" ';
						$params		= '';
						$class		= 'checkbox_row';
					
						if ($disabled) {
							$params = 'DISABLED';				
							$class	.= ' disabled';
						}
					
						echo '<div class="'.$class.'">'
							. form_radio('fields[permission_group]', $key, $selected, $params)
							. '<div class="'.$class.'_data">'
								. '<label>'
								. '<strong>' . $group['name'] . '</strong>'
								. '<p>' . $group['desc'] . '</p>'
								. '</label>'
							. '</div>'
							. '</div>';
	
						$first 		= FALSE;
					
					}
				}
			
			?>
		</div>
	</div>

	<div class="form_row" id="options_draft_email">
		<label>Receive Pending Emails:</label>
		<div class="field"><?=form_dropdown('fields[options][receive_pending_emails]', $this->CONF['options']['enabled'], $fields['options']['receive_pending_emails'])?></div>
	</div>	

	<? if ($fields['permission_group'] == 'administrator') { ?>

		<div class="form_row">
			<label>Show Application Profiler:</label>
			<div class="field"><?=form_dropdown('fields[options][display_profiler]', $this->CONF['options']['output_profiler'], $fields['options']['display_profiler'])?></div>
		</div>	
	
	<? } ?>

	<div class="form_row">
		<label>Last Logged In:</label>
		<div class="field">
			<div class="field_text">
				<? if (!empty($fields['login_date']) && ($fields['login_date'] != '0000-00-00 00:00:00')) { echo date(DATE_DISPLAY_FORMAT, strtotime($fields['login_date'])); } else { echo 'n/a'; }?>
			</div>		
		</div>
	</div>	

	<button type="submit" class="button"><span><?=(($fields[$this->id_field] < 0) ? 'CREATE' : 'SAVE')?></span></button>


</form>

<? $this->load->view('shared/content_footer'); ?>

<!-- END PAGE EDIT -->
