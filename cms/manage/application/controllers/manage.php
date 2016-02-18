<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 

require_once(APPPATH . 'controllers/application.php');

class Manage extends Application {

	var		$ADMIN_CONF;
	var 	$CONF;
	var 	$MODEL;

	var 	$zone				= 'manage';
	var 	$module;
	var 	$has_module_config	= FALSE;
	var 	$has_module_model	= FALSE;
	
	var		$require_login		= TRUE;

	var		$id_field;
	var 	$current_id;	

	var  	$admin_path;
	var  	$admin_dir;
	var 	$user_admin;
	var 	$module_path;

	function __construct() {
			parent::__construct();

			if (empty($this->module)) 	$this->module = strtolower(get_class($this));
			if (empty($this->id_field)) $this->id_field = strtolower(get_class($this)) . '_id';

			$this->ADMIN_CONF 			= $this->loadConfig('manage');
			
			// Admin variables
			$this->asset_path 			= zonepath('assets/');
			$this->admin_path			= $this->config->item('base_url');
			$this->admin_dir			= '';
			$this->user_admin			= true;
			
			if ($this->module != 'manage') {
				$this->module_path		= $this->admin_path . strtolower($this->module);
			}

			// Set timezone
			if (!empty($this->ADMIN_CONF['timezone'])) {
				date_default_timezone_set($this->ADMIN_CONF['timezone']);
			}

			$this->load->library('layout', $this->loadConfig('manage_layout', FALSE));
			$this->load->library('admin/authentication', $this->ADMIN_CONF['login']);

			$this->load->model('activity_model', 'activity');

			if ($this->require_login === TRUE) {
				$this->authentication->requireLogin(($this->module == 'admin') ? 'admin_access' : 'module_'.$this->module);
			}

			if (($this->has_module_model === TRUE) && ($this->module != __CLASS__)) {
				$name 		= $this->module.'_model';
				$this->load->model($name);
				$this->MODEL = $this->{$name};
			}

			if (($this->has_module_config === TRUE) && ($this->module != 'Application')) {
				$this->CONF = $this->loadConfig($this->module);
			}

			if ($this->input->get_post('return_url'))
				$this->session->set_flashdata('return_url', $this->input->get_post('return_url'));			

			$this->load->helper('alerts');
			$this->load->library('admin/menu_builder');

			// Set up initial layout properties
			$this->layout->setTitle($this->ADMIN_CONF['site_title']);
			$this->layout->appendTitle(get_class($this));


	}


	// ------------------------------------------------------------------------

	function index() {

			if ($this->module == 'admin') {
				redirect('');
			} else {
				//$this->output->show('<div style="height: 400px;"> </div>');
			}

	}


	// ------------------------------------------------------------------------
	// THEME CSS BUILDER	

	public function getThemeCss() {

			$theme_items	= array('highlight');
			$style_start 	= '<style type="text/css">' . NL.TAB . '<!--';
			$style_end 		= NL . TAB. '-->' . NL . TAB . '</style>';
			$output			= '';
			
			foreach ($theme_items as $item) {
			
				if (!empty($this->ADMIN_CONF['theme'][$item . '_color'])) {
				
					$color = $this->ADMIN_CONF['theme'][$item . '_color'];
				
					foreach ($this->ADMIN_CONF['theme'][$item . '_rules'] as $rule) {
						$output .= NL . TAB . TAB . sprintf($rule, $color);
					}
					
				}
			}

			return $style_start . $output . $style_end;
				
	}

	public function getAdminLogo() {
	
			if (!empty($this->ADMIN_CONF['theme']['header_image'])) {
				return '<img src="'. zonepath($this->ADMIN_CONF['theme']['header_image']) .'"/>';
			} else {
				return '<div class="logo_text">'.$this->ADMIN_CONF['site_title'].'</div>';
			}		
			
	}

	// ------------------------------------------------------------------------

	public function picker() {
		
			// Make sure this is off. Can't see it anyway.
			$this->output->enable_profiler(FALSE);
	
			$this->layout->setLayout('plain');	
			$this->layout->setBodyClass('popup');	
	
			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));		
			
			// Load data from the menu builder
			// We should be passing something to know what to select in menu
			$output = $this->menu_builder->display(null, 'picker');
		
			$this->layout->show($output);

	}


	// ------------------------------------------------------------------------
	// AJAX
	
	function ajax($name=false, $param=null) {
		
			$this->output->enable_profiler(FALSE);
			
			if (method_exists($this,'_ajax_'.$name)) {
				$output = call_user_func(array($this, '_ajax_'.$name), $param);
			} else {
				show_error(__METHOD__ . ' Invalid or missing utility name.');
			}
	
			echo $output;
		
	}

	function _ajax_menuAction($menu=null) {
	
			$id 	= $this->input->get_post('id');
			$action	= $this->input->get_post('action');
			if (empty($id)) show_error(__METHOD__ . ' Invalid or missing parameters.');

			switch($action) {
			
				case 'open':
					$this->menu_builder->addActiveItem($id, $menu);
					break;
					
				case 'close':
					$this->menu_builder->removeActiveItem($id, $menu);
					break;
				
				default:
					break;
			
			}

			return;

	}



	// ------------------------------------------------------------------------

	function nextPublishTime($format=true) {

			$conf_key = 'publish_times';

			if ($this->module == 'page') {
				$times 		= $this->CONF[$conf_key];		
			} else {
				$page_conf 	= $this->loadConfig('page');
				$times 		= $page_conf[$conf_key];		
			}
		
			$time = null;

			foreach ($times as $t => $display) {

				if ($t > date('Hi')) {
					$time = ($format) ? $display : $t;
					break;
				}
				
			}	

			return $time;
			
	}
	
	// ------------------------------------------------------------------------

	protected function displayAccessMenu() {
			if (isset($this->menu_builder)) return $this->menu_builder->display();	
	}
	
	
	// ------------------------------------------------------------------------
	
	function _output($content='', $return=FALSE) {

			$this->layout->addMeta('author', $this->SITE_CONF['author']);
			$this->layout->addMeta('generator', $this->SITE_CONF['application_name'].' '.$this->SITE_CONF['application_version']);
			$this->layout->addMeta('cms', $this->SITE_CONF['application']);

			$this->layout->addJavaScriptVar('ADMIN_PATH', $this->admin_path);
			$this->layout->addJavaScriptVar('MODULE', $this->module);
			$this->layout->addJavaScriptVar('CURRENT_ID', $this->current_id);

			if (($this->layout->_layout == 'default') && ($this->layout->getFormat() == 'html')){
	
				// This needs to happen here incase the items were built dynamically during processing.
				$this->layout->setRegionData('sidebar', $this->displayAccessMenu());
			
			}
	
			echo $this->layout->wrap($content, $return);	
	
	}

	
	
}
