<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class User_model extends Application_model {

	var $table 			= 'user';
	var $id_field		= 'user_id';

	var $sort_field 	= 'user';

	var $select_set 	= array(
							'basic'			=> array('user_id', 'user', 'email')
							, 'navigation'	=> array('user_id', 'user', 'type', 'create_date', 'enabled')
							, 'login'		=> array('user_id', 'user', 'email', 'password', 'permission_group', 'options', 'login_date', 'enabled')
							, 'password'	=> array('user_id', 'user', 'email', 'password_options', 'password')							
							);

    // ------------------------------------------------------------------------
	// EXTENDED METHODS

	public function update($fields=array()) {

			if (!count($fields)) show_error('Invalid update parameters.');

			if (!empty($fields['permission_group']) && ($fields['permission_group'] == 'administrator') && $this->authentication->get('permission_group') != 'administrator') {
				show_error('You are not allowed to edit or create Administrator users. If you feel you have reached this page in error please contact the website administrator.');	
			
			}
			
			if (!empty($fields['password_new']) && !empty($fields['password_confirm'])) {
			
				// Passwords Match?
				if ($fields['password_new'] != $fields['password_confirm'])
					show_error('New password does not match confirmation.');		
				
				// New Password Length
				if (strlen($fields['password_new']) < $this->ADMIN_CONF['login']['req_password_len'])
					show_error('Password must be '.$this->ADMIN_CONF['login']['req_password_len'].' characters or longer.');

				$fields['password'] 				= encryptString($fields['password_new']);
				$fields['password_options'] 	= $this->_setPasswordOptions($fields);

				
			}

			return parent::update($fields);		

	}

	
	public function loggedIn($login_key=null) {
	
			if (!is_null($login_key)) {
				$this->db->set('login_key', $login_key);
			}
			$this->db->set('login_date', 'NOW()', FALSE);
			$this->db->where($this->id_field, CI()->authentication->get('user_id'), FALSE);
			$this->db->update($this->table);
	
	}

	public function expire($user_id=null) {
	
			if (is_null($user_id)) return false;
				
			$fields = array(
				'user_id'		=> $user_id
				, 'login_date'	=> ''
				, 'enabled'		=> 0
				);
			$this->update($fields);	
	}
	
	// Make model specific updates to result array
	protected function _processGetRow($row=array()) {

			if (!empty($row['permission_group'])) {
				$row['permissions'] = $this->ADMIN_CONF['user_groups'][$row['permission_group']]['permissions'];
			}					

			if (!empty($row['password_options'])) {
				$row['password_options'] = json_decode($row['password_options'], TRUE);
			}					
			
			return $row;
		
	}


	public function getEmptyItem() {
			
			$item = parent::getEmptyItem();
	
			$item['type'] 			= $this->table;
			$item['options'] 	= array(
				'display_profiler'	=> 0
				);
			
			return $item;
		
	}

    // ------------------------------------------------------------------------
	// CUSTOM METHODS

	public function encryptString($string=null) {
		
		$string = $this->SITE_CONF['encryption_salt'] . $string . $this->SITE_CONF['encryption_salt'];

		// Create encryption handle. Even though this is RIJNDAEL_128 will be 256-bit because of key length
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

		if (mcrypt_generic_init($cipher, $this->SITE_CONF['encryption_key'], $this->SITE_CONF['encryption_iv']) != -1) {
		
			// PHP pads with NULL bytes if string is not a multiple of the block size
			$encrypted_raw 		= mcrypt_generic($cipher, $string);
			$encrypted_string 	= bin2hex($encrypted_raw);

			// Terminate decryption handle and close module
			mcrypt_generic_deinit($cipher);
			mcrypt_module_close($cipher);
			
		} else {
			show_error('Fatal error. Unable to set up encryption mechanism. Please contact the system administrator.');
		}

		return $encrypted_string;		
		
	}


	public function authenticate($user=null, $password=null) {
			
			if (is_null($user) || is_null($password)) return false;
	
			$fields = array('user' => $user, 'password' => $password);
			$return	= $this->get($fields, 'login');
			
			return (count($return)) ? $return[0] : FALSE;
			
	}


	public function changePassword($fields=null) {
	
			if (is_null($fields)) show_error('Invalid change password request.');
			
			// Current password correct?
			if ($this->encryptString($fields['password_current']) != $this->authentication->get('password')) {
				show_error('You have supplied an incorrect current password.');
			}
			
			$password_options = $this->authentication->get('password_options');
			
			// Check previous passwords
			// TODO: Add config to turn this on/off
			if (!empty($password_options['previous']) && in_array($this->encryptString($fields['password_new']), $password_options['previous'])) {
				show_error('Your new password can not match any of your previous 5 passwords.');
			}
			
			$fields[$this->id_field] 			= $this->authentication->get($this->id_field);

			$this->update($fields);			
			
			return true;
	
	}

	public function resetPassword($fields=null, $user=null) {
	
			if (is_null($fields)) show_error('Invalid change password request.');

			if (!empty($fields['user'])) {
				$user_fields = array('user'=>$fields['user']);
			} else if (!empty($fields['email'])) {
				$user_fields = array('email'=>$fields['email']);
			} else {
				return FALSE;
			}

			// Get user
			$user = $this->selectSet('password')->get($user_fields);

			// Make sure a valid user with email address
			if (empty($user[0][$this->id_field])) return FALSE;
			if (empty($user[0]['email'])) {
				$this->layout->setMessage('Can not reset your password, you do not have an email address supplied. Please contact your system administrator to perform this action.', 'page', 'error');
				return FALSE;
			}

			CI()->activity->log($this->table, $user[0][$this->id_field], 'Password reset');

			$new_pass		= random_string();

			$update_fields 	= array(
				'user_id'					=> $user[0][$this->id_field]
				, 'password'				=> $this->encryptString($new_pass)
				, 'password_options'	=> $user[0]['password_options']
				);
			$this->update($update_fields);


			// Send notification email
			CI()->load->library('email');
			CI()->email->from(CI()->ADMIN_CONF['forgot_pw_email']['from_email'], CI()->ADMIN_CONF['forgot_pw_email']['from_name']);
			CI()->email->to($user[0]['email']);
			CI()->email->subject(CI()->ADMIN_CONF['forgot_pw_email']['subject']);
			CI()->email->message(CI()->ADMIN_CONF['forgot_pw_email']['message'] . $new_pass);
			CI()->email->send();
			
			return true;
	
	}


	public function _setPasswordOptions($fields=null) {

			if (empty($fields['password_options'])) {
				$fields['password_options'] 				= array();
			}

			$fields['password_options']['set']				= date('m-d-Y, H:i:s', time());

			if (empty($fields['password_options']['previous'])) {
				$fields['password_options']['previous'] 	= array();
			} else {
				$fields['password_options']['previous']	= array_slice(array_reverse($fields['password_options']['previous']), 0, 5);
			}

			// Add new password to history
			array_push($fields['password_options']['previous'], $fields['password']);
			
			return $fields['password_options'];
	
	}

	
}
