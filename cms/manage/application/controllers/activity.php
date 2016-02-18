<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Activity extends Manage {

	var	$has_module_config	= TRUE;

	var $filter_module		= null;
	var $filter_id			= null;
	
	var $activity_link		= null;

	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

			$this->activity_link = $this->admin_path . 'activity/';

	}


	// ------------------------------------------------------------------------

	public function _remap($filter_module=null) {

			$fields = array();
	
			if (($filter_module == 'by') && $this->uri->segment(4)) {
					$fields['user_id'] 	= $this->uri->segment(4);
			} else {
			
				if (!is_null($filter_module) && ($filter_module != 'index')) {
					$fields['module'] 		= $filter_module;
					$this->filter_module	= $filter_module;
				}
		
				if ($this->uri->segment(4) && is_numeric($this->uri->segment(4))) {
					$fields['module_id'] 	= $this->uri->segment(4);
					$this->filter_id		= $this->uri->segment(4);
				}
	
			}
	
			$items = $this->activity->get($fields, NULL, TRUE);		
			
			$this->load->view('activity/list', array('items' => $items));

	}


	public function getUserDropdown() {
			return 'hello';
	}

}