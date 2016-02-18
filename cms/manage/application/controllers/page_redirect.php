<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Page_redirect extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= TRUE;
	
	var $id_field            = 'redirect_id';

	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();
			
	}


	// ------------------------------------------------------------------------

	function index() {

			$items 			= $this->MODEL->selectSet('navigation')->doAutoJoin(FALSE)->get();
			$view_data 		= array(
				'items' 	=> $items
				);	

			$this->load->view($this->module.'/index', $view_data);	

			return true;

	}
	
	// ------------------------------------------------------------------------
	// CREATE

	public function create() {
			$this->edit(-1);
	}


	public function update($id=null, $fields=null) {

			$this->current_id 	= $id;
			$save 				= true;
			$fields 			= is_array($fields) ? $fields : $this->input->post('fields');

			$this->layout->setLayout('blank');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');

			$update_page = $this->MODEL->update($fields);
			$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');

			$url = $this->module.'/';
		
		    redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

	}
	

	public function edit($id=null) {

			if (!$id) show_error(__METHOD__ . ' Invalid or missing item id.');

			$this->current_id 		= $id;
			
			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			

            // Edit existing page
            $item 				    = $this->MODEL->first()->getById($id);
            $edit_params['fields'] 	= $item;

			$this->load->view($this->module.'/edit', $edit_params);	

			return true;

	}

	
	// ------------------------------------------------------------------------
	// DELETE 
	
	public function delete($id=false) {

			$this->authentication->requirePermission('global_delete');

			$this->layout->setLayout('blank');
		
			if (($this->input->get($this->id_field) == $id) && ($this->input->get('DELETE') == 'DELETE') && ($id > 1)) {
				
				$this->MODEL->delete($id);
			
				$this->session->set_flashdata('message', ucwords($this->module).' deleted.');
				
				$url = $this->module;
				redirect($url, 'location');
				echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';


			} else {
				show_error(__METHOD__ . ' Invalid params');
			}
	
	}

}