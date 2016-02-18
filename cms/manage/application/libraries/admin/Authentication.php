<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
// ------------------------------------------------------------------------

class Authentication {
	
	private	$_user;
	private $_conf;
	private	$_initial_login = FALSE;

	function __construct($conf=null) {
	
			if (is_null($conf)) show_error(__METHOD__ . ' Could not initiate library, no config supplied.');
			
			$this->_conf = $conf;
	
			CI()->load->model('user_model');	
	}


	// ------------------------------------------------------------------------
	// REQUIRE LOGIN

	function requireLogin($require_permission=null, $redirect=TRUE) {

			CI()->load->model('user_model');

			$username 	= null;
			$password 	= null;
			$action		= null;

			if (CI()->input->post('username') && CI()->input->post('password')) {

				// This is a request from the login form, authenticate.
				$username 				= CI()->input->post('username');
				$password 				= CI()->input->post('password');

				$this->_initial_login 	= TRUE;

			} else if (CI()->session->userdata('username') && CI()->session->userdata('password')) {
			
				// User already logged in. Authenticate against session values.
				$username 				= CI()->session->userdata('username');
				$password 				= CI()->session->userdata('password');

			}

			if ($this->_login($username, $password)) {
			
				// SUCCESSFUL LOGIN

				// Was there a intitial permission requirement?
				if (!is_null($require_permission)) $this->requirePermission($require_permission);	
				
				return TRUE;
			
			} else {
				
				// User not athenticated
				
				// Make sure you are viewing the login form
				if ($redirect && (CI()->module != 'login')) {
				
					$redirect_uri = '/' . CI()->zone . CI()->uri->uri_string;
				
					CI()->session->set_flashdata('page_message', 'You could not be logged in. Pleae check your username and password.');			
					CI()->session->set_flashdata('redirect_uri', $redirect_uri);
				
					// Only do redirection if this is a normal request.
					if (!CI()->is_ajax) redirect($this->_conf['path']);

					// Should never see this, but just in case.
					exit('AUTHENTICATION REQUIRED');
					
				}
			
			}			
			
	}


	// ------------------------------------------------------------------------
	// SIMPLE LOGIN

	public function login($username, $password) {
	
			$this->_initial_login 	= TRUE;

			return (($this->_login($username, $password)) ? TRUE : FALSE);
	
	}

	function _login($username, $password) {
			
			if (is_null($username) || is_null($password)) return FALSE;			

			if ($this->isInitial()) {

				// Encrypt password if this is a login request, it will already be encrypted if session val
				$password 	= encryptString($password,$username);
				
				$this->_checkLoginFails();
			
			}

			if ($login = CI()->user_model->authenticate($username, $password)) {
				
				// Found user row, but make sure everything is ok now...

				// Check if the user is enabled. Need to do it here for a nicer error message
				$this->_checkInactiveLogin($login);

				if ($this->isInitial()) {

					// Has user been active in past 90 days
					$this->_checkExpiredLogin($login);
					
					// Has password expired ?
				
				}

				// User login successful, sweet!
			
				CI()->session->set_userdata('username', $username);
				CI()->session->set_userdata('password', $password);

				$this->_user = $login;

				// Set user options
				if (!empty($this->_user['options']['display_profiler'])
					&& ($this->_user['options']['display_profiler'] > 0)) {
					CI()->output->enable_profiler(TRUE);
				}
				
				// Everything's good, let's get out of here
				return TRUE;	

			} else {
				
				// Failed login attempt
				// Update tracking table with information
				$this->_addLoginFail($username);
			
			}

			return FALSE;
	
	}
	
	private function _checkExpiredLogin($login) {
	
			// Has user been active in past 90 days
			if ($this->_conf['limit_inactive_login'] 
				&& is_numeric($this->_conf['limit_inactive_login']) && !empty($login['login_date']) && ($login['login_date'] != '0000-00-00 00:00:00')) {
	
				if ((strtotime('now') - strtotime($login['login_date']))/(86400) > $this->_conf['limit_inactive_login']) {

					if ($this->_conf['log_user_activity'])
						CI()->activity->log('user', $login['user_id'], 'Disabling account because of expired login attempt.');

					CI()->user_model->expire($login['user_id']);

					$this->logout('');
	
					show_error('Your account has been disabled because of '.CI()->ADMIN_CONF['limit_inactive_login'].' days of inactivity. To active your account please contact the system administrator.');
				
				}
				
			}
	
	}

	private function _checkInactiveLogin($login) {
	
		if ($login['enabled'] != 1) {
			show_error('Your account has been disabled. To active your account please contact the system administrator.');
		}
	
	}	
	
	private function _checkExpiredPassword($login) {
				
			if ($this->_conf['limit_password_age'] 
				&& is_numeric($this->_conf['limit_password_age_days']) 
				&& !empty($login['password_options']['set'])) {

				if ((time() - $login['password_options']['set'])/(86400) > $this->_conf['limit_password_age_days']) {

					if (CI()->input->post('fields') && CI()->authentication->resetPassword(CI()->input->post('fields'))) {
					
						// Do nothing, user is in the middle of changing password
	
					} else {
	
						$message		= 'Your password has not been changed in the past '. $this->_conf['limit_password_age_days'] .' days. You must set a new password to continue.';
						
						$form_action 	= CI()->admin_path . 'login/change/';
						CI()->output->showView('shared/change_password', array('form_action'=>$form_action, 'message'=>$message));
						
						exit(CI()->output->final_output);
						
					}
				}
			}
	
	}
	
	private function _checkLoginFails($ip=null) { 
	
			if (!$this->_conf['limit_failed_login']) return;

			if (is_null($ip)) $ip = CI()->input->ip_address();

			$limit_days = $this->_conf['limit_failed_mins'];

			$query		= CI()->db->query("SELECT count(ip) as count FROM user_login_fail WHERE ip = '".$ip."' AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL ".$limit_days." MINUTE)");
			$results	= $query->result_array();
			$count 		= (!empty($results[0]['count'])) ? $results[0]['count'] : 0;
			
			if ($count > $this->_conf['limit_failed_cnt']) {
				$this->logout();
				show_error('You have had too many failed login attempts. Please wait 30 minutes before trying again. If you need assistance please contact the system administrator.');
			}
	
	}

	private function _addLoginFail($username=null, $ip=null) { 

			if (!$this->_conf['limit_failed_login']) return;

			if (is_null($ip)) $ip = CI()->input->ip_address();
			
			// TODO: Also add log_message call

			CI()->db->query("INSERT INTO user_login_fail (ip, date, user) VALUES ('".$ip."', NOW(), '".$username."')");
	
	}

	public function clearLoginFail() { 

			CI()->db->query("TRUNCATE TABLE user_login_fail");
	
	}


	// ------------------------------------------------------------------------
	// LOGOUT

	function logout($message=null) {

			CI()->layout->setLayout('plain');	

			$this->_user = array();

			CI()->session->unset_userdata('username');
			CI()->session->unset_userdata('password');
			CI()->session->sess_destroy();

			$message	= is_null($message) ? 'You have been logged out.' : $message;

			CI()->session->set_flashdata('page_message', $message);			
	
	
	}
	
	
	// ------------------------------------------------------------------------
	// PERMISSION CHECKS
	
	public function requirePermission($request=null) { 
	
			if ($this->hasPermission($request)) return TRUE;

			log_message('error', '** PERMISSION VIOLATION (User: ' . $this->_user['user'] . ' / Action: '.$request . ')');			
			show_error('You do not have permission to access this portion of the website. <br/>Please contact the system administrator.<br/><br/>Error code: '.$request);
			exit();

	}
	
	public function hasPermission($request=null) { 
	
			if (is_null($request)) 		return FALSE;
			if (!is_array($request)) 	$request = array($request);

			foreach ($request as $check) {
				if (!$this->_hasPermission($check)) return FALSE;
			}
				
			return TRUE;

	
	}

	private function _hasPermission($request=null) { 
	
			if (count($this->_user['permissions']) &&
				(in_array('*', $this->_user['permissions']) || in_array($request, $this->_user['permissions'])
				)) return TRUE;
				
			return FALSE;
	
	}

	public function isLoggedIn() {
			return !empty($this->_user['user_id']) ? TRUE : FALSE;
	}


    // ------------------------------------------------------------------------
	// SIMPLE GETTER/SETTER METHODS

	public function get($item) {
			return !empty($this->_user[$item]) ? $this->_user[$item] : FALSE;
	}
	
	public function setInitial($value=FALSE) {
	
			$this->_initial_login = $value;
			return $this;		
	
	}
	
	protected function isInitial() {
			return $this->_initial_login;
	}

}