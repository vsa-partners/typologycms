<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 

require_once(APPPATH . 'controllers/manage.php');

class Login extends Manage {

	var $require_login = FALSE;

	// ------------------------------------------------------------------------

	function index() {
	
			// Make sure the user is not already logged in. 
			if ($this->authentication->isLoggedIn()) {
				// Already logged in
				redirect('/');
			}

			$form_action = $this->session->flashdata('redirect_uri') ? reduce_multiples(SITEPATH . $this->session->flashdata('redirect_uri'), '/') : $this->config->item('base_url');
	
			$this->layout->appendTitle('Please Login');		

			$this->layout->setLayout('plain');	
			$this->layout->setBodyClass('popup plain');	
			
			$this->load->view('login/index', array('form_action' => $form_action));
	
	}

	public function expire() {
			
			$this->activity->log('user', $this->authentication->get('user_id'), 'Deactivating expired session.');

			$message = 'You have been logged out because of inactivity';
			$this->authentication->logout($message);
			redirect($this->ADMIN_CONF['login']['path']);	
	
	}

	public function logout($message=null) {
	
			$this->authentication->logout($message);

			redirect($this->ADMIN_CONF['login']['path']);	
	
	}

	public function forgot() {

			$this->output->enable_profiler(TRUE);
	
			if ($fields = $this->input->post('fields')) {
				if ($this->user_model->resetPassword($fields)) {
					$this->logout('Your password has been reset. Please check your email.');
				} else {
					$this->layout->setMessage('User not found.', 'modal', 'error');
				}
			}

			$this->layout->appendTitle('Forgot Password');
			$this->layout->setLayout('plain');	
			$this->layout->setBodyClass('popup plain');	

			$form_action = $this->admin_path . $this->module . '/forgot';

			$this->load->view('login/forgot_password', array('form_action' => $form_action));
	
	
	}

	public function change() {
	
			// Make sure the user is logged in
			$this->authentication->requireLogin();

			$this->layout->appendTitle('Change Password');

			if ($fields = $this->input->post('fields')) {
				if ($this->user_model->changePassword($fields)) {
					$this->logout('Your password has been changed. Please re-login.');
				}				
			}
				
			$form_action = $this->admin_path . $this->module . '/change';

			$this->load->view('login/change_password', array('form_action' => $form_action));
			
	}
	
	
}
