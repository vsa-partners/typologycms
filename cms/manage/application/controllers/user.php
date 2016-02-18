<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class User extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= TRUE;

	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();
	}


	// ------------------------------------------------------------------------
	// EDIT

	public function edit($id=null) {

			if (!$id) show_error(__METHOD__ . ' Invalid or missing item id.');
			
			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			
		 
			$edit_params = array();
			
			// Save Info
			$this->current_id 	= $id;

			$edit_item 				= $this->MODEL->first()->getById($id);
			$edit_params['fields'] 	= $edit_item;
			
			// Make sure we have valid items
			if (empty($edit_params['fields'][$this->id_field])) show_error('Could not edit. Invalid item returned.');

			$this->load->view('user/edit', $edit_params);	

			return true;

	}


	// ------------------------------------------------------------------------
	// UPDATE

	public function update($id=null, $fields=null) {
	
			$this->current_id 	= $id;
			$fields 			= is_array($fields) ? $fields : $this->input->post('fields');

			$this->layout->setLayout('plain');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');

			// TODO: Validation
			
			if (true) {

				// Save Item
				$update_item = $this->MODEL->update($fields);

				$this->session->set_flashdata('message', 'Your item has been saved.');

			}

			// Add activity record
			$this->activity->log($this->module, $update_item[$this->id_field], (($id < 1) ? 'Create User' : 'Update User'));
			
			// Successful, return to edit page.
			$url = $this->module.'/edit/'.$update_item[$this->id_field];
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

	}


	// ------------------------------------------------------------------------
	// CREATE

	public function create() {
		
			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));
		 
			$edit_params = array();
			
			// Save Info
			$this->current_id 	= -1;

			$edit_item 				= $this->MODEL->getEmptyItem();
			$edit_params['fields'] 	= $edit_item;
			
			$this->load->view('user/edit', $edit_params);	

			return true;

	}

	// ------------------------------------------------------------------------
	// DELETE

	public function delete($id=false) {

			$this->authentication->requirePermission('global_delete');
		
			$this->layout->setLayout('plain');

			if (($this->input->get($this->id_field) == $id) && ($this->input->get('DELETE') == 'DELETE') && ($id > 1)) {

				$this->MODEL->delete($id);

				$this->session->set_flashdata('message', 'Item deleted.');

				// Add activity record
				$this->activity->log($this->module, $id, 'Delete User');

				// Successful, return to admin.
				$url = $this->module;
				redirect($url, 'location');
				echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';


			} else {
				show_error(__METHOD__ . ' Invalid params');
			}
	
	}


}