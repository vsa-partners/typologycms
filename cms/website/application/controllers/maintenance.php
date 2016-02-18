<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 

require_once(APPPATH . 'controllers/application.php');

class Maintenance extends Application {

	var $ADMIN_CONF;

	var $module 			= 'maintanance';

	var $output_template 	= '<h3>%s</h3>%s<br/><br/><hr/>';

	// ------------------------------------------------------------------------


	public function __construct() {
			parent::__construct();

			$this->ADMIN_CONF 	= $this->loadConfig('manage');
	
	}


	function index() {
			// Nothing here. Should we throw an error?
			echo 'Nothing here.';
	}


	// ------------------------------------------------------------------------


	function tasks($period=null) {
	
			if ($this->ADMIN_CONF['cron_key'] != $this->input->get('key')) {
				// TODO: Send email
				exit('ACCESS DENIED');
			}

			$this->load->library('site_maintenance');

			$output = '';
			
			switch ($period) {

			
				case 'daily':
					$output .= sprintf($this->output_template, 'Send Pending Pages Email', $this->sendPendingEmail());
					
					break;
				
				case 'monthly':
					$output .= sprintf($this->output_template, 'Clear HTML/XML Cache', $this->site_maintenance->clearSiteCache());
					$output .= sprintf($this->output_template, 'Clear JS/CSS Cache', $this->site_maintenance->clearAssetCache());
					$output .= sprintf($this->output_template, 'Clear Tmp Files', $this->site_maintenance->clearTmpFiles());
					$output .= sprintf($this->output_template, 'Delete Old Page Versions', $this->site_maintenance->deletePageVersions());
					$output .= sprintf($this->output_template, 'Clear System Logs', $this->site_maintenance->clearLogs());
					break;
				default:
					break;
			}

			if (strlen($output)) {
				$this->_sendOutputEmail($output, $period);
				echo $output;
			}

	
	}


	// ------------------------------------------------------------------------


	function _sendOutputEmail($data, $period=null) {
			
			if (!empty($this->ADMIN_CONF['cron_alert_email'])) {

				$this->load->library('email', array('mailtype'=>'html'));
				$this->email->from($this->ADMIN_CONF['default_from_email'], $this->SITE_CONF['site_title'] . ' CMS');
				$this->email->to($this->ADMIN_CONF['cron_alert_email']);
				$this->email->subject('Cron Report: '.ucwords($period));
				$this->email->message($data);
				$this->email->send();			
			
			}	
	
	}

	function sendPendingEmail() {
	
			// TODO: Check if option is enabled in config

			$this->load->model('user_model');
	
			$emails = array();

			// Loop through all users and fecth emails
			foreach ($this->user_model->get(array()) as $user) {
				if (!empty($user['options']['receive_pending_emails']) && ($user['options']['receive_pending_emails'] == 1)) {
					if (!empty($user['email'])) $emails[] = $user['email'];
				}
			}

			if (count($emails)) {

				$this->load->model('page_model');
				$pages 	= $this->page_model->getPending(1000, 'basic');
	
				if (count($pages)) {
	
					$view_params = array(
						'pages' 		=> $pages
						, 'module_path' => $this->config->item('base_url') . 'page/'
						);

					$email_data = $this->load->view('emails/workflow_daily', $view_params, TRUE);
	
					// Send email
					$this->load->library('email', array('mailtype'=>'html'));
					$this->email->from($this->ADMIN_CONF['default_from_email'], $this->ADMIN_CONF['site_title'] . ' CMS');
					$this->email->to($emails);
					$this->email->subject('CMS Pages Pending Approval');
					$this->email->message($email_data);
					$this->email->send();
					
					return 'EMAIL SENT!';
				
				}
			
			}
	
	}
	
	
	function updatePaths() {
		
		die;
		
		
		echo "<pre>";
		
		$parent_id = 11769;// 9354;//9261; //9216;//9215;
		
		$this->load->model('page_model');
		
		$page = $this->page_model->get(array('page_id' => $parent_id));
	
		// get children
		
		$children = $this->page_model->get(array('parent_id' => $parent_id));
		
		foreach ($children as $child) {
		
			$new_path = $page[0]['path']."/".$child['file_name'];
			
			echo "update ".$child['page_id']." (".$child['title'].") with <strong>".$new_path."</strong><br /><br />";
			$fields = array('page_id' => $child['page_id'], 'path' => $new_path);
			$this->page_model->update($fields);
			
			$this->_doUpdate($child['page_id'], $new_path);
		}
	}

	
	
	private function _doUpdate($page_id, $parent_path) {
		
		$children = $this->page_model->get(array('parent_id' => $page_id));
		
		foreach ($children as $child) {
						
			$new_path = $parent_path."/".$child['file_name'];
		
			echo "--- update ".$child['page_id']." (".$child['title'].") with <strong>".$new_path."</strong><br /><br />";
			$fields = array('page_id' => $child['page_id'], 'path' => $new_path);
			$this->page_model->update($fields);
			
			$this->_doUpdate2($child['page_id'], $new_path);
		
		}
		echo "<br />";
	}
	
	
	
	private function _doUpdate2($page_id, $parent_path) {
	
		$children = $this->page_model->get(array('parent_id' => $page_id));
		
		foreach ($children as $child) {
		
			$new_path = $parent_path."/".$child['file_name'];

			echo "--- --- update ".$child['page_id']." (".$child['title'].") with <strong>".$new_path."</strong><br />";
			$fields = array('page_id' => $child['page_id'], 'path' => $new_path);
			$this->page_model->update($fields);
		}
		echo "<br />";
	}
	

	
	
	
	
	
}
