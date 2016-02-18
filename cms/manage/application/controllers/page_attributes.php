<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Page_attributes extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= FALSE;
	
	var $id_field            = 'page_attributegroup_id';

	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();
			
			$this->load->model('page_attributegroup_model');			
			$this->load->model('page_attributevalue_model');

			$this->MODEL = $this->page_attributegroup_model;


	}


	// ------------------------------------------------------------------------

	function index() {
	


	}
	
	// ------------------------------------------------------------------------
	// CREATE

	public function create() {
	
			$this->authentication->requirePermission('global_create');

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			
	
			$this->load->view('page_attributes/create');	
			
	}


	public function update($id=null, $fields=null) {

			$this->current_id 	= $id;
			$save 				= true;
			$fields 			= is_array($fields) ? $fields : $this->input->post('fields');

			$this->layout->setLayout('blank');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');

			$update_page = $this->MODEL->update($fields);
			$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');

			if ($this->session->flashdata('return_url')) {
				$url = $this->session->flashdata('return_url');
			} else {
				$url = $this->module.'/edit/'.$update_page[$this->id_field];
			}
		
		    redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

	}
	

	public function update_value($id=null, $fields=null) {

			$this->current_id 	= $id;
			$save 				= true;
			$fields 			= is_array($fields) ? $fields : $this->input->post('fields');

			$this->layout->setLayout('blank');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');

			$update_page = $this->page_attributevalue_model->update($fields);
			$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');

			$url = $this->module.'/edit_value/'.$update_page['page_attributevalue_id'];
		
		    redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

	}

	public function edit($id=null) {

			if (!$id) show_error(__METHOD__ . ' Invalid or missing item id.');
			
			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			

            // Edit existing page
            $item 				    = $this->MODEL->first()->getByIdWithValues($id);
            $edit_params['fields'] 	= $item;

			$this->load->view('page_attributes/edit', $edit_params);	

			return true;

	}

	public function edit_value($id) {

			if (!$id) show_error(__METHOD__ . ' Invalid or missing item id.');
			
			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			
			
			$item 			= array();
			$item['fields'] = $this->page_attributevalue_model->first()->getById($id);

			$this->load->view('page_attributes/edit_value', $item);	

			return true;


	}


	
	// ------------------------------------------------------------------------
	// DELETE 
	
	public function delete_value($id=false) {

			$this->authentication->requirePermission('global_delete');

			$this->layout->setLayout('blank');
		
			if (($this->input->get('page_attributevalue_id') == $id) && ($this->input->get('DELETE') == 'DELETE') && ($id > 1)) {
				
				$this->page_attributevalue_model->delete($id);
			
				$this->session->set_flashdata('message', ucwords($this->module).' deleted.');
				
				$url = $this->module;
				redirect($url, 'location');
				echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';


			} else {
				show_error(__METHOD__ . ' Invalid params');
			}
	
	}

	// ------------------------------------------------------------------------


	
	protected function displayAccessMenu() {
	        $items = $this->MODEL->getAllGroups();
			return $this->menu_builder->display($items);	
	}



}