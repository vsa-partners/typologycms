<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Template extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= TRUE;

    
    
	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

     		$this->load->model('page_attributegroup_model');

	}

    
    
	// ------------------------------------------------------------------------
	// EDIT

	public function edit($id=null, $mode=null, $post=null) {

			if (!$id) show_error(__METHOD__ . ' Invalid or missing item id.');
			
			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			
			
			$this->PAGE_CONF 	= $this->loadConfig('page');

		 
			$edit_params = array();
			
			// Save Info
			$this->current_id 	= $id;

			// TODO: Check user edit permissions ?

			if ($id < 0) {

				// Create new item
				$item_params = array(
					'type' 	=> $this->input->get_post('type')
					);					
				$edit_params['fields'] 	= $this->template_model->getEmptyItem($item_params);

			} else {
			
				// Edit existing page
				$page_item 				= $this->template_model->first()->getById($id);
				$edit_params['fields'] 	= $page_item;

			}

			// Make sure we have valid items
			if (empty($edit_params['fields'][$this->id_field])) show_error('Could not edit. Invalid item returned.');

			// Set path, the menu will need this
			if (!empty($edit_params['fields']['template_path'])) $this->current_path = $edit_params['fields']['template_path'];
			
			// Get list of all templates for dropdown
			$edit_params['all_templates'] = $this->template_model->getForDropdown(array());
			
			// Get list of all attribuge groups for dropdown
			$edit_params['all_attributegroups'] = $this->page_attributegroup_model->getAllGroupsForDropdown(array());
			
			// Edit Template

			$this->load->view('template/edit', $edit_params);	

			return true;

	}

	// ------------------------------------------------------------------------
	// LINKS

	public function links($id=null) {

			if (!$id) show_error(__METHOD__ . ' Invalid or missing item id.');

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			
		 
			$edit_params 			= array();
			
			$this->current_id 		= $id;
			$page_item 				= $this->template_model->related('page')->first()->getById($id, 'navigation');
			$edit_params['fields'] 	= $page_item;
			
			// Make sure we have valid items
			if (empty($edit_params['fields'][$this->id_field])) show_error('Could not edit. Invalid item returned.');

			// Edit Links
			$this->load->view('template/links', $edit_params);	

			return true;

	}


	// ------------------------------------------------------------------------
	// UPDATE

	public function update($id=null, $fields=null) {

			$this->current_id 	= $id;
			$save 				= true;
			$fields 			= is_array($fields) ? $fields : $this->input->post('fields');

			$this->layout->setLayout('blank');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');

			$update_page = $this->template_model->update($fields);
			$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');

			if ($this->session->flashdata('return_url')) {
				$url = $this->session->flashdata('return_url');
			} else {
				$url = $this->module.'/edit/'.$update_page[$this->id_field];
			}
		
		    redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

	}




	// ------------------------------------------------------------------------
	// CREATE

	public function create($parent=null) {
			$this->edit(-1);
	}
	
	// ------------------------------------------------------------------------
	// DELETE 
	
	public function delete($id=false) {

			$this->authentication->requirePermission('global_delete');

			$this->layout->setLayout('blank');
		
			if (($this->input->get($this->id_field) == $id) && ($this->input->get('DELETE') == 'DELETE') && ($id > 1)) {
				
				$this->template_model->delete($id);
			
				$this->session->set_flashdata('message', ucwords($this->module).' deleted.');
				
				$url = $this->module;
				redirect($url, 'location');
				echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';


			} else {
				show_error(__METHOD__ . ' Invalid params');
			}
	
	}


}