<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
class Application extends Controller {

	var 	$SITE_CONF;

	var 	$asset_path;
	var 	$current_uri;

	var 	$is_ajax			= FALSE;

	private	$_use_db_config		= TRUE;
	private $_db_config_items 	= array();


	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();
			
			$this->checkPHPVersion();			

			$this->load->helper(array('form', 'url', 'date', 'html_entities', 'string', 'encryption'));
			$this->load->library(array('session', 'xsl_transform'));

			$this->load->model('config_model');

			$this->SITE_CONF 	= $this->loadConfig('website');

			// Show output profiler?
			if ($this->input->get('show_profiler')) $this->output->enable_profiler(TRUE);

			$this->current_uri 	= reduce_multiples(SITEPATH . $this->uri->uri_string(), '/');

			// Set timezone
			if (!empty($this->SITE_CONF['timezone'])) {
				date_default_timezone_set($this->SITE_CONF['timezone']);
			}

			if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
				$this->is_ajax = TRUE;
			}

	}


	// ------------------------------------------------------------------------
	// LOAD CONFIG
	// Loads config values from external file then updates with values stored in database
	
	function loadConfig($name, $db=true) {
	
			if (empty($name)) show_error(__METHOD__ . ' : Invalid or missing config name supplied.');

			// Load config items from file - the traditional way
			$this->load->config('app_' . $name, TRUE, TRUE);

			$file_conf 	= $this->config->item('app_' . $name);

			if (!$file_conf) 				return array();
			if (!$this->_use_db_config) 	return $file_conf;
		
			// Make sure the db config items are loaded and in memory
			if (!count($this->_db_config_items) && $this->_use_db_config)
				$this->_db_config_items	= $this->config_model->get();

			foreach ($this->_db_config_items as $conf) {
				if ($conf['zone'] != $name) continue;
				
				// Support for booleans coming from form input
				if (!is_array($conf['value']) && ((strtolower(trim($conf['value'])) === 'false') || ($conf['value'] == '0'))) {
					$conf['value'] = FALSE;
				} else if (!is_array($conf['value']) && ((strtolower(trim($conf['value'])) === 'true') || ($conf['value'] == '1'))) {
					$conf['value'] = TRUE;			
				}			

				if ($conf['zone_group'] != 'general') {

					if (!isset($file_conf[$conf['zone_group']][$conf['name']])) {
						pr('Empty File Config: ('.$name.') '.$conf['zone_group'].'/'.$conf['name']);
					}

					$file_conf[$conf['zone_group']][$conf['name']] = $conf['value'];
				} else {

					if (!isset($file_conf[$conf['name']])) {
						pr('Empty File Config: ('.$name.') '.$conf['name']);
					}

					$file_conf[$conf['name']] = $conf['value'];
				}
			}
			
			return $file_conf;
	
	}




	// ------------------------------------------------------------------------


	function checkPHPVersion() {
			if (floor(phpversion()) < 5) show_error('This application required PHP Version 5 to be installed. Please contact your administrator.');
	}

	function index() {
			show_error('This is app index. Shouldn\'t be here.');	
	}

}